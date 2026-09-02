<?php

namespace App\Http\Controllers;

use App\Models\Avia;
use App\Models\Avto;
use App\Models\Zh;
use App\Models\CityDelivery;
use App\Models\Invoice;
use Illuminate\Http\Request;

class AjaxController extends Controller
{
    public function searchCityDelivery(Request $request)
    {
        $search = $request->input('search', '');
        $cities = CityDelivery::where('title', 'like', "%{$search}%")->get(['id', 'title']);
        return response()->json($cities);
    }

    public function calcDeliveryCar(Request $request)
    {
        $from = $request->input('package_from');
        $to = $request->input('package_to');
        $route = Avto::where('city_from', $from)->where('city_to', $to)->first();
        return response()->json($route ? ['price' => $route->price, 'time' => $route->time] : ['price' => null]);
    }

    public function calcDeliveryAir(Request $request)
    {
        $from = $request->input('package_from');
        $to = $request->input('package_to');
        $route = Avia::where('city_from', $from)->where('city_to', $to)->first();
        return response()->json($route ? ['price' => $route->price, 'time' => $route->time] : ['price' => null]);
    }

    public function calcDeliveryZd(Request $request)
    {
        $from = $request->input('package_from');
        $to = $request->input('package_to');
        $route = Zh::where('city_from', $from)->where('city_to', $to)->first();
        return response()->json($route ? ['price' => $route->price, 'time' => $route->time] : ['price' => null]);
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

    /**
     * Публичная накладная в PDF по её номеру — со страницы отслеживания.
     * По решению владельца доступна без авторизации.
     */
    public function trackInvoicePdf($number)
    {
        if (!ctype_digit((string) $number)) {
            abort(404);
        }

        $invoice = Invoice::where('invoice_number', $number)->firstOrFail();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('api.invoice_pdf', compact('invoice'))
            ->setPaper('a4', 'portrait');

        return $pdf->download("nakladnaya_{$invoice->invoice_number}.pdf");
    }

    public function sendFrom(Request $request)
    {
        $phone = $request->input('phone');
        // TODO: отправка уведомления (email/telegram)
        return response()->json(['success' => true]);
    }
}
