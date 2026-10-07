<?php

namespace App\Support;

use App\Models\Invoice;
use App\Models\InvoiceEvent;
use App\Models\Staff;
use Illuminate\Support\Carbon;

/**
 * Отчёт по курьерам за период: кто сколько забрал, принял в пункте и доставил.
 * Источник — журнал накладных (invoice_events), действия из мобилки:
 * pickup — забрал у отправителя, destination_pickup — принял на складе
 * назначения, delivery — доставил получателю.
 */
class CourierReport
{
    public const ACTIONS = [
        'pickup' => 'Забрал у отправителя',
        'destination_pickup' => 'Принял в пункте назначения',
        'delivery' => 'Доставил получателю',
    ];

    /** Быстрые периоды: ключ => [название, с, по]. */
    public static function presets(): array
    {
        $today = Carbon::today();

        return [
            'today' => ['Сегодня', $today->copy(), $today->copy()],
            'yesterday' => ['Вчера', $today->copy()->subDay(), $today->copy()->subDay()],
            'week' => ['7 дней', $today->copy()->subDays(6), $today->copy()],
            'month' => ['Этот месяц', $today->copy()->startOfMonth(), $today->copy()],
            'prev_month' => ['Прошлый месяц', $today->copy()->subMonthNoOverflow()->startOfMonth(), $today->copy()->subMonthNoOverflow()->endOfMonth()->startOfDay()],
        ];
    }

    /**
     * @return array{rows: list<array>, totals: array}
     */
    public function build(Carbon $from, Carbon $to, ?int $staffId = null): array
    {
        $events = InvoiceEvent::query()
            ->whereIn('event', array_keys(self::ACTIONS))
            ->where('actor_type', 'staff')
            ->whereNotNull('actor_id')
            ->whereBetween('created_at', [$from->copy()->startOfDay(), $to->copy()->endOfDay()])
            ->when($staffId, fn ($q) => $q->where('actor_id', $staffId))
            ->orderBy('created_at')
            ->get();

        $invoices = Invoice::whereIn('id', $events->pluck('invoice_id')->unique())->get()->keyBy('id');
        $staff = Staff::whereIn('id', $events->pluck('actor_id')->unique())->get()->keyBy('id');

        $rows = [];
        foreach ($events as $ev) {
            $inv = $invoices[$ev->invoice_id] ?? null;
            if (! $inv) {
                continue; // накладную удалили
            }
            $id = (int) $ev->actor_id;
            $rows[$id] ??= [
                'id' => $id,
                'name' => optional($staff[$id] ?? null)->full_name ?: ($ev->actor_name ?: 'Сотрудник #' . $id),
                'counts' => array_fill_keys(array_keys(self::ACTIONS), 0),
                'delivered_weight' => 0.0,
                'delivered_places' => 0,
                'items' => [],
            ];
            $row = &$rows[$id];
            $row['counts'][$ev->event]++;
            if ($ev->event === 'delivery') {
                $row['delivered_weight'] += (float) $inv->weight;
                $row['delivered_places'] += (int) $inv->quantity;
            }
            $row['items'][] = [
                'at' => $ev->created_at,
                'action' => $ev->event,
                'action_label' => self::ACTIONS[$ev->event],
                'invoice_id' => $inv->id,
                'number' => (string) $inv->invoice_number,
                'from' => (string) $inv->sender_city,
                'to' => (string) $inv->recipient_city,
                'recipient' => trim((string) ($inv->recipient_company ?: $inv->recipient_name)),
                'weight' => (float) $inv->weight,
                'places' => (int) $inv->quantity,
                'local' => $inv->isLocalDelivery(),
            ];
            unset($row);
        }

        // Больше доставок — выше.
        usort($rows, fn ($a, $b) => [$b['counts']['delivery'], $b['counts']['pickup']] <=> [$a['counts']['delivery'], $a['counts']['pickup']]);

        $totals = [
            'counts' => array_fill_keys(array_keys(self::ACTIONS), 0),
            'delivered_weight' => 0.0,
            'delivered_places' => 0,
        ];
        foreach ($rows as $r) {
            foreach ($r['counts'] as $k => $n) {
                $totals['counts'][$k] += $n;
            }
            $totals['delivered_weight'] += $r['delivered_weight'];
            $totals['delivered_places'] += $r['delivered_places'];
        }

        return ['rows' => $rows, 'totals' => $totals];
    }
}
