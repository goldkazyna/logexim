@extends('layouts.cabinet')
@section('title', 'Отчёты по накладным')
@push('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
<style>
    .common_btn { background-color: #D0171C; border-color: #D0171C; border-radius: 10px; color: #ffffff; padding: 8px 20px; border: none; cursor: pointer; text-decoration: none; display: inline-block; }
    .common_btn:hover { background-color: #a21216; }
    .icon-btn { font-size: 20px; cursor: pointer; color: #D0171C; }
    .icon-btn:hover { color: #a21216; }
    .date-filter { margin-bottom: 20px; display: flex; gap: 20px; align-items: center; }
</style>
@endpush
@section('content')
<main class="flex-grow p-6">
    <div class="flex items-center justify-between flex-wrap gap-2 mb-6">
        <div>
            <div class="card overflow-hidden">
                <div class="card-header">
                    <h4 class="text-slate-900 text-lg font-medium mb-2">Отчеты по накладным</h4>
                </div>
                <div class="p-4">
                    <form method="GET" action="{{ url('cabinet/reports') }}">
                        <div class="date-filter">
                            <label for="start_date">Дата начала:</label>
                            <input type="date" name="start_date" id="start_date" class="form-input" value="{{ $start_date ?? '' }}" required>

                            <label for="end_date">Дата окончания:</label>
                            <input type="date" name="end_date" id="end_date" class="form-input" value="{{ $end_date ?? '' }}" required>

                            <button type="submit" class="btn btn-primary common_btn">Поиск</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Display Invoices -->
            <div class="card overflow-hidden mt-4">
                <div class="card-header">
                    <h4 class="card-title">Результаты поиска</h4>
                </div>
                <div class="overflow-x-auto">
                    <div class="min-w-full inline-block align-middle">
                        <div class="overflow-hidden">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-start text-sm text-gray-500">Дата</th>
                                        <th scope="col" class="px-6 py-3 text-start text-sm text-gray-500">Номер накладной</th>
                                        <th scope="col" class="px-6 py-3 text-start text-sm text-gray-500">Отправитель</th>
                                        <th scope="col" class="px-6 py-3 text-start text-sm text-gray-500">Получатель</th>
                                        <th scope="col" class="px-6 py-3 text-start text-sm text-gray-500">Количество мест</th>
                                        <th scope="col" class="px-6 py-3 text-start text-sm text-gray-500">Вес (кг)</th>
                                        <th scope="col" class="px-6 py-3 text-start text-sm text-gray-500">Объявленная ценность</th>
                                        <th scope="col" class="px-6 py-3 text-start text-sm text-gray-500">Статус</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @forelse($invoices as $invoice)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                            {{ \Carbon\Carbon::parse($invoice->date)->format('d.m.Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                            {{ $invoice->invoice_number }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                            {{ $invoice->sender_company }}<br>
                                            {{ $invoice->sender_name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                            {{ $invoice->recipient_company }}<br>
                                            {{ $invoice->recipient_name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                            {{ $invoice->quantity }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                            {{ $invoice->weight }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                            {{ $invoice->declared_value }} KZT
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                            @switch($invoice->status)
                                                @case(0) Заявка создана @break
                                                @case(1) Принята в работу @break
                                                @case(2) Отправлено @break
                                                @case(3) Исполнена @break
                                                @default Неизвестный статус
                                            @endswitch
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="9" class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800">Накладные не найдены.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Export Button -->
            @if(count($invoices) > 0)
            <div class="mt-4 text-right">
                <form method="GET" action="{{ url('cabinet/export_invoices') }}">
                    <input type="hidden" name="start_date" value="{{ $start_date }}">
                    <input type="hidden" name="end_date" value="{{ $end_date }}">
                    <button type="submit" class="btn btn-primary common_btn">Экспорт в Excel</button>
                </form>
            </div>
            @endif
        </div>
    </div>
</main>
@endsection
@push('scripts')
<script>
    $(document).ready(function() {
        setTimeout(function() { $('#success-alert').fadeOut('slow'); }, 5000);
    });
</script>
@endpush
