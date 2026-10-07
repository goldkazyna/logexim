<?php

namespace App\Http\Controllers;

use App\Models\CityDelivery;
use App\Models\Invoice;
use App\Support\DeliveryCalculator;
use Illuminate\Http\Request;

class AjaxController extends Controller
{
    public function searchCityDelivery(Request $request)
    {
        $search = $request->input('search', '');
        $cities = CityDelivery::where('title', 'like', "%{$search}%")->get(['id', 'title']);
        return response()->json($cities);
    }

    /** Калькулятор на главной — правила в App\Support\DeliveryCalculator. */
    public function calcDelivery(Request $request, DeliveryCalculator $calculator)
    {
        $data = $request->validate([
            'package_from' => 'required|integer',
            'package_to' => 'required|integer',
            'transport' => 'required|in:car,railway,air',
            'weight' => 'nullable|numeric|min:0|max:100000',
            'length' => 'nullable|numeric|min:0|max:10000',
            'width' => 'nullable|numeric|min:0|max:10000',
            'height' => 'nullable|numeric|min:0|max:10000',
            'non_stackable' => 'nullable|boolean',
        ]);

        return response()->json($calculator->calculate(
            $data['transport'], (int) $data['package_from'], (int) $data['package_to'], $data,
        ));
    }

    /**
     * Публичное отслеживание накладной по её номеру («Найти посылку» на сайте).
     *
     * Отдаём только статус и обезличенные детали — см. Invoice::publicTracking().
     */
    public function trackInvoice(Request $request)
    {
        $number = trim((string) $request->input('invoice_number', ''));

        if ($number === '' || ! ctype_digit($number)) {
            return response()->json(['found' => false]);
        }

        $invoice = Invoice::with('events')->where('invoice_number', $number)->first();

        if (! $invoice) {
            return response()->json(['found' => false]);
        }

        return response()->json([
            'found' => true,
            'invoice' => $invoice->publicTracking(),
        ]);
    }

    public function sendFrom(Request $request)
    {
        $phone = $request->input('phone');
        // TODO: отправка уведомления (email/telegram)
        return response()->json(['success' => true]);
    }
}
