<?php

namespace App\Services;

use App\Models\CityDelivery;
use App\Models\Invoice;
use App\Models\InvoiceEvent;
use App\Models\Staff;

/**
 * Автоназначение курьеров по городу при создании накладной.
 *
 * Город отправления → курьер отправки (courier_id), город назначения →
 * курьер в пункте (receiving_courier_id). Берём активного сотрудника,
 * обслуживающего этот город (см. staff_city). Если такого нет — поле
 * остаётся пустым, ничего не назначаем.
 */
class CourierAssigner
{
    public function assign(Invoice $invoice): void
    {
        $changes = [];

        if (empty($invoice->courier_id)) {
            $id = $this->pick($invoice->sender_city, 'courier');
            if ($id) {
                $changes['courier_id'] = $id;
            }
        }

        if (empty($invoice->receiving_courier_id)) {
            $id = $this->pick($invoice->recipient_city, 'agent');
            if ($id) {
                $changes['receiving_courier_id'] = $id;
            }
        }

        if ($changes === []) {
            return;
        }

        // Назначили курьера отправки, а этап ещё «Заявка создана» — двигаем на
        // «Назначен курьер», как это делает ручное назначение в админке.
        if (isset($changes['courier_id']) && (int) $invoice->detail_status === 0) {
            $changes['detail_status'] = 1;
        }

        $invoice->forceFill($changes)->saveQuietly();

        if (isset($changes['courier_id'])) {
            $this->log($invoice, 'courier_assigned', $changes['courier_id']);
        }
        if (isset($changes['receiving_courier_id'])) {
            $this->log($invoice, 'receiving_courier_assigned', $changes['receiving_courier_id']);
        }
    }

    /**
     * Активный сотрудник, обслуживающий город. Из нескольких предпочитаем того,
     * у кого есть подходящая роль ($prefer: courier для отправки, agent для
     * пункта назначения), иначе — первого по id.
     */
    private function pick(?string $city, string $prefer): ?int
    {
        $city = trim((string) $city);
        if ($city === '') {
            return null;
        }

        $cityId = CityDelivery::where('title', $city)->value('id');
        if (! $cityId) {
            return null;
        }

        $candidates = Staff::where('active', 1)
            ->whereHas('cities', fn ($q) => $q->where('city_delivery.id', $cityId))
            ->orderBy('id')
            ->get()
            ->filter(fn (Staff $s) => $s->isCourierRole());

        if ($candidates->isEmpty()) {
            return null;
        }

        $preferred = $candidates->first(fn (Staff $s) => in_array($prefer, $s->roleNames(), true));

        return ($preferred ?? $candidates->first())->id;
    }

    private function log(Invoice $invoice, string $event, int $staffId): void
    {
        InvoiceEvent::create([
            'invoice_id' => $invoice->id,
            'event' => $event,
            'actor_type' => 'system',
            'actor_role' => null,
            'actor_name' => 'Автоназначение по городу',
            'meta' => [
                'staff_id' => $staffId,
                'staff_name' => optional(Staff::find($staffId))->full_name,
            ],
            'created_at' => now(),
        ]);
    }
}
