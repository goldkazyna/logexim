<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class StaffInvoiceController extends Controller
{
    // Сопоставление detail_status (0..6) к ключу для mobile UI
    private const STATUS_KEY_BY_DETAIL = [
        0 => 'assigned', // теоретически у курьера не бывает
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

        $query = Invoice::with('courier')->orderBy('id', 'desc');

        if ($staff->role === 'courier') {
            $query->where('courier_id', $staff->id);
        }

        $invoices = $query->limit(200)->get();

        return response()->json([
            'invoices' => $invoices->map(fn ($inv) => $this->present($inv))->values(),
        ]);
    }

    public function show(Request $request, $id)
    {
        $staff = $request->user();
        $invoice = Invoice::with('courier')->findOrFail($id);

        if ($staff->role === 'courier' && (int) $invoice->courier_id !== (int) $staff->id) {
            return response()->json(['message' => 'Нет доступа'], 403);
        }

        return response()->json(['invoice' => $this->present($invoice, full: true)]);
    }

    public function findByNumber(Request $request, $number)
    {
        $staff = $request->user();
        $invoice = Invoice::with('courier')->where('invoice_number', $number)->first();

        if (!$invoice) {
            return response()->json(['message' => 'Накладная не найдена'], 404);
        }
        if ($staff->role === 'courier' && (int) $invoice->courier_id !== (int) $staff->id) {
            return response()->json(['message' => 'Накладная не назначена вам'], 403);
        }

        return response()->json(['invoice' => $this->present($invoice, full: true)]);
    }

    public function pickup(Request $request, $id)
    {
        $staff = $request->user();
        $invoice = Invoice::findOrFail($id);

        if ($staff->role === 'courier' && (int) $invoice->courier_id !== (int) $staff->id) {
            return response()->json(['message' => 'Накладная не назначена вам'], 403);
        }

        $data = $request->validate([
            'signature' => 'required|string',
        ]);

        $decoded = $this->decodeBase64Image($data['signature']);
        if ($decoded === null) {
            return response()->json(['message' => 'Некорректные данные подписи'], 422);
        }

        $relativePath = $this->saveSignatureAsWebp($invoice->id, $decoded);

        $invoice->update([
            'pickup_signature' => $relativePath,
            'detail_status' => 2, // Курьер забрал
        ]);

        return response()->json([
            'message' => 'Забор подтверждён',
            'invoice' => $this->present($invoice->fresh('courier'), full: true),
        ]);
    }

    /**
     * Принимает data-uri ("data:image/png;base64,...") или чистый base64
     * и возвращает бинарные данные изображения или null при ошибке.
     */
    private function decodeBase64Image(string $input): ?string
    {
        $clean = preg_replace('/^data:image\/[a-zA-Z0-9+]+;base64,/', '', $input);
        $bin = base64_decode($clean ?? '', true);
        if ($bin === false || strlen($bin) < 20) {
            return null;
        }
        return $bin;
    }

    private function saveSignatureAsWebp(int $invoiceId, string $imageBinary): string
    {
        $dir = 'signatures/pickup';
        $filename = "{$invoiceId}_" . date('Ymd_His') . '.webp';
        $relative = "{$dir}/{$filename}";

        $im = @imagecreatefromstring($imageBinary);
        if ($im === false) {
            // fallback — сохраняем как есть
            Storage::disk('public')->put(
                str_replace('.webp', '.png', $relative),
                $imageBinary,
            );
            return str_replace('.webp', '.png', $relative);
        }

        imagesavealpha($im, true);
        imagepalettetotruecolor($im);

        $tmp = tempnam(sys_get_temp_dir(), 'sig_') . '.webp';
        imagewebp($im, $tmp, 85);
        imagedestroy($im);

        $binary = file_get_contents($tmp);
        @unlink($tmp);

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
            'sender' => [
                'name' => (string) $inv->sender_name,
                'company' => (string) $inv->sender_company,
                'phone' => (string) $inv->sender_phone,
                'address' => $this->composeAddress(
                    $inv->sender_address,
                    $inv->sender_city,
                    $inv->sender_region,
                    $inv->sender_country,
                ),
            ],
            'recipient' => [
                'name' => (string) $inv->recipient_name,
                'company' => (string) $inv->recipient_company,
                'phone' => (string) $inv->recipient_phone,
                'address' => $this->composeAddress(
                    $inv->recipient_address,
                    $inv->recipient_city,
                    $inv->recipient_region,
                    $inv->recipient_country,
                ),
            ],
            'cargo' => [
                'description' => (string) $inv->description,
                'weight' => $inv->weight !== null ? $inv->weight . ' кг' : '',
                'quantity' => $inv->quantity !== null ? $inv->quantity . ' мест' : '',
            ],
        ];

        if ($full) {
            $data['plan_date'] = $inv->plan_date;
            $data['fact_date'] = $inv->fact_date;
            $data['special'] = (string) $inv->special;
            $data['pickup_signature_url'] = $inv->pickup_signature
                ? Storage::disk('public')->url($inv->pickup_signature)
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
