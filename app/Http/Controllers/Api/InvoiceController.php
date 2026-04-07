<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = Invoice::where('user_id', $user->id)->orderBy('id', 'desc');

        if ($request->has('search') && $request->input('search') !== '') {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhere('sender_name', 'like', "%{$search}%")
                  ->orWhere('recipient_name', 'like', "%{$search}%")
                  ->orWhere('recipient_company', 'like', "%{$search}%");
            });
        }

        $invoices = $query->paginate(15);

        return response()->json([
            'invoices' => $invoices->items(),
            'pagination' => [
                'current_page' => $invoices->currentPage(),
                'last_page' => $invoices->lastPage(),
                'total' => $invoices->total(),
            ],
        ]);
    }

    public function store(Request $request)
    {
        try {
            $user = $request->user();
            $lastNumber = Invoice::max('invoice_number');
            $newNumber = $lastNumber ? $lastNumber + 1 : 900001;

            Invoice::create([
                'user_id' => $user->id,
                'status' => 0,
                'invoice_number' => $newNumber,
                'date' => $request->input('date', now()->format('Y-m-d')),
                'sender_name' => $request->input('sender_name', ''),
                'sender_phone' => $request->input('sender_phone', ''),
                'sender_company' => $request->input('sender_company', ''),
                'sender_city' => $request->input('sender_city', ''),
                'sender_country' => $request->input('sender_country', ''),
                'sender_region' => $request->input('sender_region', ''),
                'sender_district' => $request->input('sender_district', ''),
                'sender_address' => $request->input('sender_address', ''),
                'recipient_name' => $request->input('recipient_name', ''),
                'recipient_phone' => $request->input('recipient_phone', ''),
                'recipient_company' => $request->input('recipient_company', ''),
                'recipient_city' => $request->input('recipient_city', ''),
                'recipient_country' => $request->input('recipient_country', ''),
                'recipient_region' => $request->input('recipient_region', ''),
                'recipient_district' => $request->input('recipient_district', ''),
                'recipient_address' => $request->input('recipient_address', ''),
                'description' => $request->input('description', ''),
                'quantity' => $request->input('quantity', 1),
                'weight' => $request->input('weight', 0),
                'volume_weight' => $request->input('volume_weight', 0),
                'fragile' => $request->input('fragile', 0) ? 1 : 0,
                'declared_value' => $request->input('declared_value', 0),
                'payment' => 0,
                'payment_sender' => $request->input('payment_sender', 0) ? 1 : 0,
                'payment_recipient' => $request->input('payment_recipient', 0) ? 1 : 0,
                'payment_contract' => $request->input('payment_contract', 0) ? 1 : 0,
                'payment_invoice' => $request->input('payment_invoice', 0) ? 1 : 0,
                'payment_cash' => $request->input('payment_cash', 0) ? 1 : 0,
                'special' => $request->input('special', ''),
            ]);

            $this->sendTelegram("Новая накладная (моб. приложение), №: {$newNumber}");

            return response()->json([
                'message' => 'Заявка успешно создана',
                'invoice_number' => $newNumber,
            ], 201);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Ошибка при создании заявки. Попробуйте позже.',
            ], 500);
        }
    }

    public function show(Request $request, $id)
    {
        $invoice = Invoice::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        return response()->json(['invoice' => $invoice]);
    }

    public function stats(Request $request)
    {
        $userId = $request->user()->id;

        // Statuses: 0=Заявка создана, 1=Принята в работу, 2=Отправлено, 3=Исполнена, 4=Отменена
        return response()->json([
            'active' => Invoice::where('user_id', $userId)->whereIn('status', [0, 1])->count(),
            'in_transit' => Invoice::where('user_id', $userId)->where('status', 2)->count(),
            'delivered' => Invoice::where('user_id', $userId)->where('status', 3)->count(),
            'total' => Invoice::where('user_id', $userId)->count(),
        ]);
    }

    private function sendTelegram(string $message): void
    {
        try {
            $token = '7328907732:AAGhdEnWZfpOp22hczseO1TjiKoB19pZB4g';
            $chatId = -1002187416306;
            Http::post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $message,
            ]);
        } catch (\Throwable) {
            // Silent fail
        }
    }
}
