<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoiceEvent;
use App\Models\Staff;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StaffInvoiceController extends Controller
{
    private const STATUS_KEY_BY_DETAIL = [
        0 => 'assigned',
        1 => 'assigned',
        2 => 'in_delivery',
        3 => 'in_delivery',
        4 => 'in_delivery',
        5 => 'in_delivery',
        6 => 'delivered',
    ];

    public function index(Request $request)
    {
        $staff = $request->user();
        $query = Invoice::with(['courier', 'warehouse', 'receivingCourier'])->orderBy('id', 'desc');

        if ($staff->isCourierRole()) {
            $query->where(function ($q) use ($staff) {
                $q->where('courier_id', $staff->id)
                  ->orWhere('receiving_courier_id', $staff->id);
            });
        } elseif ($staff->role === 'warehouse') {
            // Кладовщик видит: что сейчас на приёмку (detail=2) + что у него на складе (detail=3, warehouse_id=он)
            $query->where(function ($q) use ($staff) {
                $q->where('detail_status', 2)
                  ->orWhere(function ($q2) use ($staff) {
                      $q2->where('detail_status', 3)->where('warehouse_id', $staff->id);
                  });
            });
        }

        $invoices = $query->limit(200)->get();

        return response()->json([
            'invoices' => $invoices->map(fn ($inv) => $this->present($inv))->values(),
        ]);
    }

    public function show(Request $request, $id)
    {
        $staff = $request->user();
        $invoice = Invoice::with(['courier', 'warehouse', 'receivingCourier', 'events'])->findOrFail($id);

        if ($staff->isCourierRole() && !$this->courierCanAccess($invoice, $staff)) {
            return response()->json(['message' => 'Нет доступа'], 403);
        }

        return response()->json(['invoice' => $this->present($invoice, full: true)]);
    }

    public function findByNumber(Request $request, $number)
    {
        $staff = $request->user();
        $invoice = Invoice::with(['courier', 'warehouse', 'receivingCourier'])
            ->where('invoice_number', $number)->first();

        if (!$invoice) {
            return response()->json(['message' => 'Накладная не найдена'], 404);
        }
        if ($staff->isCourierRole() && !$this->courierCanAccess($invoice, $staff)) {
            return response()->json(['message' => 'Накладная не назначена вам'], 403);
        }

        return response()->json(['invoice' => $this->present($invoice, full: true)]);
    }

    private function courierCanAccess(Invoice $invoice, $staff): bool
    {
        return (int) $invoice->courier_id === (int) $staff->id
            || (int) $invoice->receiving_courier_id === (int) $staff->id;
    }

    // POST /api/staff/invoices/{id}/pickup — курьер забрал
    public function pickup(Request $request, $id)
    {
        $staff = $request->user();
        $invoice = Invoice::findOrFail($id);

        if (!$staff->isCourierRole()) {
            return response()->json(['message' => 'Действие доступно только курьеру или агенту'], 403);
        }
        if ((int) $invoice->courier_id !== (int) $staff->id) {
            return response()->json(['message' => 'Накладная не назначена вам'], 403);
        }

        // State machine: забор возможен только из состояний «Заявка создана» или «Назначен курьер»
        $from = (int) $invoice->detail_status;
        if ($from > 1) {
            return response()->json([
                'message' => 'Накладная уже забрана — текущий этап: ' .
                    (Invoice::DETAIL_STATUSES[$from] ?? '—'),
            ], 422);
        }

        $data = $request->validate(['signature' => 'required|string']);
        $decoded = $this->decodeBase64Image($data['signature']);
        if ($decoded === null) {
            return response()->json(['message' => 'Некорректные данные подписи'], 422);
        }

        $relativePath = $this->saveSignatureAsWebp($invoice->id, $decoded);

        $invoice->update([
            'pickup_signature' => $relativePath,
            'detail_status' => 2,
        ]);

        $this->logEvent($invoice, $staff, 'pickup', $from, 2, [
            'signature_path' => $relativePath,
        ]);

        return response()->json([
            'message' => 'Забор подтверждён',
            'invoice' => $this->present($invoice->fresh(['courier', 'warehouse']), full: true),
        ]);
    }

    // POST /api/staff/invoices/{id}/receive — кладовщик принял на склад
    public function receive(Request $request, $id)
    {
        $staff = $request->user();
        $invoice = Invoice::findOrFail($id);

        if ($staff->role !== 'warehouse') {
            return response()->json(['message' => 'Действие доступно только кладовщику'], 403);
        }

        $from = (int) $invoice->detail_status;
        if ($from !== 2) {
            return response()->json([
                'message' => $from < 2
                    ? 'Накладная ещё не забрана курьером'
                    : 'Накладная уже принята или дальше — текущий этап: ' .
                      (Invoice::DETAIL_STATUSES[$from] ?? '—'),
            ], 422);
        }

        $invoice->update([
            'warehouse_id' => $staff->id,
            'received_at' => now(),
            'detail_status' => 3,
        ]);

        $this->logEvent($invoice, $staff, 'warehouse_receive', $from, 3, [
            'warehouse_location' => $staff->warehouse_location,
        ]);

        return response()->json([
            'message' => 'Принято на склад',
            'invoice' => $this->present($invoice->fresh(['courier', 'warehouse']), full: true),
        ]);
    }

    // POST /api/staff/invoices/{id}/ship — кладовщик отправил со склада
    public function ship(Request $request, $id)
    {
        $staff = $request->user();
        $invoice = Invoice::findOrFail($id);

        if ($staff->role !== 'warehouse') {
            return response()->json(['message' => 'Действие доступно только кладовщику'], 403);
        }

        $from = (int) $invoice->detail_status;
        if ($from !== 3) {
            return response()->json([
                'message' => $from < 3
                    ? 'Накладная ещё не принята на склад'
                    : 'Накладная уже отправлена или дальше — текущий этап: ' .
                      (Invoice::DETAIL_STATUSES[$from] ?? '—'),
            ], 422);
        }
        if ((int) $invoice->warehouse_id !== (int) $staff->id) {
            return response()->json([
                'message' => 'Накладная принята другим кладовщиком',
            ], 403);
        }

        $invoice->update([
            'shipped_at' => now(),
            'detail_status' => 4,
        ]);

        $this->logEvent($invoice, $staff, 'warehouse_ship', $from, 4, [
            'warehouse_location' => $staff->warehouse_location,
        ]);

        return response()->json([
            'message' => 'Отправлено со склада',
            'invoice' => $this->present($invoice->fresh(['courier', 'warehouse']), full: true),
        ]);
    }

    // POST /api/staff/invoices/{id}/destination-pickup — курьер-получатель забрал со склада
    public function destinationPickup(Request $request, $id)
    {
        $staff = $request->user();
        $invoice = Invoice::findOrFail($id);

        if (!$staff->isCourierRole()) {
            return response()->json(['message' => 'Действие доступно только курьеру или агенту'], 403);
        }
        if ((int) $invoice->receiving_courier_id !== (int) $staff->id) {
            return response()->json(['message' => 'Вы не назначены принимающим курьером этой накладной'], 403);
        }

        $from = (int) $invoice->detail_status;
        if ($from !== 4) {
            return response()->json([
                'message' => $from < 4
                    ? 'Накладная ещё не отправлена со склада'
                    : 'Накладная уже получена курьером — текущий этап: ' .
                      (Invoice::DETAIL_STATUSES[$from] ?? '—'),
            ], 422);
        }

        $invoice->update(['detail_status' => 5]);
        $this->logEvent($invoice, $staff, 'destination_pickup', $from, 5);

        return response()->json([
            'message' => 'Груз принят курьером',
            'invoice' => $this->present($invoice->fresh(['courier', 'warehouse', 'receivingCourier']), full: true),
        ]);
    }

    // POST /api/staff/invoices/{id}/deliver — курьер-получатель доставил, подпись получателя
    public function deliver(Request $request, $id)
    {
        $staff = $request->user();
        $invoice = Invoice::findOrFail($id);

        if (!$staff->isCourierRole()) {
            return response()->json(['message' => 'Действие доступно только курьеру или агенту'], 403);
        }
        if ((int) $invoice->receiving_courier_id !== (int) $staff->id) {
            return response()->json(['message' => 'Вы не назначены принимающим курьером этой накладной'], 403);
        }

        $from = (int) $invoice->detail_status;
        if ($from !== 5) {
            return response()->json([
                'message' => $from < 5
                    ? 'Накладная ещё не получена курьером в пункте назначения'
                    : 'Накладная уже доставлена',
            ], 422);
        }

        $data = $request->validate(['signature' => 'required|string']);
        $decoded = $this->decodeBase64Image($data['signature']);
        if ($decoded === null) {
            return response()->json(['message' => 'Некорректные данные подписи'], 422);
        }

        $relativePath = $this->saveSignatureAsWebp($invoice->id, $decoded, 'delivery');

        $invoice->update([
            'delivery_signature' => $relativePath,
            'delivered_at' => now(),
            'detail_status' => 6,
            'status' => 3, // Исполнена
        ]);

        $this->logEvent($invoice, $staff, 'delivery', $from, 6, [
            'signature_path' => $relativePath,
        ]);

        return response()->json([
            'message' => 'Накладная доставлена',
            'invoice' => $this->present($invoice->fresh(['courier', 'warehouse', 'receivingCourier']), full: true),
        ]);
    }

    // GET /api/staff/dashboard — сводка для кладовщика
    public function dashboard(Request $request)
    {
        $staff = $request->user();
        if ($staff->role !== 'warehouse') {
            return response()->json(['message' => 'Доступно только кладовщику'], 403);
        }

        $toReceive = Invoice::where('detail_status', 2)->count();
        $toShip = Invoice::where('detail_status', 3)->where('warehouse_id', $staff->id)->count();
        $today = InvoiceEvent::whereIn('event', ['warehouse_receive', 'warehouse_ship'])
            ->where('actor_id', $staff->id)
            ->whereDate('created_at', now()->toDateString())
            ->count();

        $recent = InvoiceEvent::with('invoice')
            ->whereIn('event', ['warehouse_receive', 'warehouse_ship'])
            ->where('actor_id', $staff->id)
            ->orderBy('id', 'desc')
            ->limit(20)
            ->get()
            ->map(fn ($e) => [
                'id' => $e->id,
                'event' => $e->event,
                'event_label' => $e->label(),
                'invoice_number' => $e->invoice?->invoice_number,
                'invoice_id' => $e->invoice_id,
                'description' => $e->invoice?->description,
                'at' => optional($e->created_at)?->format('d.m.Y H:i'),
            ])
            ->values();

        return response()->json([
            'counts' => [
                'to_receive' => $toReceive,
                'to_ship' => $toShip,
                'today' => $today,
            ],
            'recent' => $recent,
            'warehouse' => [
                'location' => $staff->warehouse_location,
                'full_name' => $staff->full_name,
            ],
        ]);
    }

    // GET /api/staff/history — история операций кладовщика
    public function history(Request $request)
    {
        $staff = $request->user();
        if ($staff->role !== 'warehouse') {
            return response()->json(['message' => 'Доступно только кладовщику'], 403);
        }

        $period = $request->query('period', 'week'); // today | week | month
        $from = match ($period) {
            'today' => now()->startOfDay(),
            'month' => now()->subMonth(),
            default => now()->subWeek(),
        };

        $events = InvoiceEvent::with('invoice')
            ->whereIn('event', ['warehouse_receive', 'warehouse_ship'])
            ->where('actor_id', $staff->id)
            ->where('created_at', '>=', $from)
            ->orderBy('id', 'desc')
            ->limit(500)
            ->get()
            ->map(fn ($e) => [
                'id' => $e->id,
                'event' => $e->event,
                'event_label' => $e->label(),
                'invoice_number' => $e->invoice?->invoice_number,
                'invoice_id' => $e->invoice_id,
                'description' => $e->invoice?->description,
                'at' => optional($e->created_at)?->format('d.m.Y H:i'),
                'date' => optional($e->created_at)?->format('Y-m-d'),
            ])
            ->values();

        return response()->json(['events' => $events]);
    }

    // ---- helpers ----

    public static function logEvent(Invoice $invoice, $staff, string $event, ?int $fromDetail, ?int $toDetail, array $meta = []): void
    {
        InvoiceEvent::create([
            'invoice_id' => $invoice->id,
            'event' => $event,
            'from_detail_status' => $fromDetail,
            'to_detail_status' => $toDetail,
            'actor_type' => $staff ? 'staff' : 'admin',
            'actor_id' => $staff?->id,
            'actor_role' => $staff?->role,
            'actor_name' => $staff?->full_name,
            'meta' => $meta ?: null,
            'created_at' => now(),
        ]);
    }

    private function decodeBase64Image(string $input): ?string
    {
        $clean = preg_replace('/^data:image\/[a-zA-Z0-9+]+;base64,/', '', $input);
        $bin = base64_decode($clean ?? '', true);
        if ($bin === false || strlen($bin) < 20) {
            return null;
        }
        return $bin;
    }

    private function saveSignatureAsWebp(int $invoiceId, string $imageBinary, string $kind = 'pickup'): string
    {
        $dir = "signatures/{$kind}";
        $filename = "{$invoiceId}_" . date('Ymd_His') . '.webp';
        $relative = "{$dir}/{$filename}";

        $im = @imagecreatefromstring($imageBinary);
        if ($im === false) {
            Storage::disk('public')->put(str_replace('.webp', '.png', $relative), $imageBinary);
            return str_replace('.webp', '.png', $relative);
        }

        imagesavealpha($im, true);
        imagepalettetotruecolor($im);

        ob_start();
        $ok = imagewebp($im, null, 85);
        $binary = ob_get_clean();
        imagedestroy($im);

        if (!$ok || $binary === false || $binary === '') {
            $pngPath = str_replace('.webp', '.png', $relative);
            Storage::disk('public')->put($pngPath, $imageBinary);
            return $pngPath;
        }

        Storage::disk('public')->put($relative, $binary);
        return $relative;
    }

    private function present(Invoice $inv, bool $full = false): array
    {
        $createdAt = '';
        try {
            if (!empty($inv->created_at)) {
                $createdAt = Carbon::parse($inv->created_at)->format('d.m.Y H:i');
            } elseif (!empty($inv->date)) {
                $createdAt = Carbon::parse($inv->date)->format('d.m.Y');
            }
        } catch (\Throwable) {
            $createdAt = (string) ($inv->created_at ?? $inv->date ?? '');
        }
        $detailStatus = (int) ($inv->detail_status ?? 0);
        $statusKey = self::STATUS_KEY_BY_DETAIL[$detailStatus] ?? 'assigned';

        $data = [
            'id' => $inv->id,
            'number' => $inv->invoice_number,
            'status' => (int) $inv->status,
            'detail_status' => $detailStatus,
            'detail_status_label' => Invoice::DETAIL_STATUSES[$detailStatus] ?? '',
            'status_key' => $statusKey,
            'created_at' => $createdAt,
            'courier_id' => $inv->courier_id ? (int) $inv->courier_id : null,
            'courier_name' => optional($inv->courier)->full_name,
            'receiving_courier_id' => $inv->receiving_courier_id ? (int) $inv->receiving_courier_id : null,
            'receiving_courier_name' => optional($inv->receivingCourier)->full_name,
            'warehouse_name' => optional($inv->warehouse)->full_name,
            'warehouse_location' => optional($inv->warehouse)->warehouse_location,
            'sender' => [
                'name' => (string) $inv->sender_name,
                'company' => (string) $inv->sender_company,
                'phone' => (string) $inv->sender_phone,
                'address' => (string) $inv->sender_address,
                'city' => (string) $inv->sender_city,
                'region' => (string) $inv->sender_region,
                'district' => (string) $inv->sender_district,
                'country' => (string) $inv->sender_country,
                'full_address' => $this->composeAddress(
                    $inv->sender_address, $inv->sender_city, $inv->sender_region, $inv->sender_country,
                ),
            ],
            'recipient' => [
                'name' => (string) $inv->recipient_name,
                'company' => (string) $inv->recipient_company,
                'phone' => (string) $inv->recipient_phone,
                'address' => (string) $inv->recipient_address,
                'city' => (string) $inv->recipient_city,
                'region' => (string) $inv->recipient_region,
                'district' => (string) $inv->recipient_district,
                'country' => (string) $inv->recipient_country,
                'full_address' => $this->composeAddress(
                    $inv->recipient_address, $inv->recipient_city, $inv->recipient_region, $inv->recipient_country,
                ),
            ],
            'cargo' => [
                'description' => (string) $inv->description,
                'weight' => $inv->weight !== null ? $inv->weight . ' кг' : '',
                'volume_weight' => $inv->volume_weight !== null && (string) $inv->volume_weight !== ''
                    ? $inv->volume_weight . ' кг' : '',
                'quantity' => $inv->quantity !== null ? $inv->quantity . ' мест' : '',
                'fragile' => (bool) $inv->fragile,
            ],
            'special' => (string) $inv->special,
        ];

        if ($full) {
            $data['plan_date'] = $inv->plan_date;
            $data['fact_date'] = $inv->fact_date;
            $data['special'] = (string) $inv->special;
            $data['received_at'] = optional($inv->received_at)?->format('d.m.Y H:i');
            $data['shipped_at'] = optional($inv->shipped_at)?->format('d.m.Y H:i');
            $data['delivered_at'] = optional($inv->delivered_at)?->format('d.m.Y H:i');
            $data['pickup_signature_url'] = $inv->pickup_signature
                ? Storage::disk('public')->url($inv->pickup_signature)
                : null;
            $data['delivery_signature_url'] = $inv->delivery_signature
                ? Storage::disk('public')->url($inv->delivery_signature)
                : null;
        }

        return $data;
    }

    private function composeAddress(?string $address, ?string $city, ?string $region, ?string $country): string
    {
        return collect([$address, $city, $region, $country])
            ->filter(fn ($p) => is_string($p) && trim($p) !== '')
            ->implode(', ');
    }
}
