@extends('layouts.admin')
@section('title', 'Накладная №' . $invoice->invoice_number)
@push('styles')
<style>
    .inv-section { background: #D0171C; color: #fff; text-align: center; border-radius: 10px; padding: 6px; margin: 20px 0 10px; font-weight: 700; font-size: 15px; }
    .inv-row { display: flex; gap: 20px; margin-bottom: 10px; font-size: 14px; }
    .inv-row .label { width: 200px; color: #888; flex-shrink: 0; }
    .inv-row .value { font-weight: 600; color: #333; }
    .inv-back { margin-bottom: 20px; }
    .inv-back a { color: #D0171C; text-decoration: none; font-weight: 600; }
    .inv-back a:hover { text-decoration: underline; }
</style>
@endpush
@section('content')
<div class="inv-back"><a href="/admin/invoices">&larr; Назад к списку</a></div>
<div class="card">
    <div class="card-header">Накладная № {{ $invoice->invoice_number }}</div>
    <div class="card-body">
        <div class="inv-row"><div class="label">Дата:</div><div class="value">{{ \Carbon\Carbon::parse($invoice->date)->format('d.m.Y') }}</div></div>
        <div class="inv-row"><div class="label">Статус:</div><div class="value">
            @switch($invoice->status)
                @case(0) Заявка создана @break
                @case(1) Принята в работу @break
                @case(2) Отправлено @break
                @case(3) Исполнена @break
                @case(4) Отменена @break
            @endswitch
        </div></div>

        <div class="inv-section">Отправитель</div>
        <div class="inv-row"><div class="label">ФИО отправителя:</div><div class="value">{{ $invoice->sender_name }}</div></div>
        <div class="inv-row"><div class="label">Компания:</div><div class="value">{{ $invoice->sender_company }}</div></div>
        <div class="inv-row"><div class="label">Телефон:</div><div class="value">{{ $invoice->sender_phone }}</div></div>
        <div class="inv-row"><div class="label">Адрес:</div><div class="value">{{ $invoice->sender_address }}, {{ $invoice->sender_city }}, {{ $invoice->sender_region }}, {{ $invoice->sender_country }}</div></div>

        <div class="inv-section">Получатель</div>
        <div class="inv-row"><div class="label">ФИО получателя:</div><div class="value">{{ $invoice->recipient_name }}</div></div>
        <div class="inv-row"><div class="label">Компания:</div><div class="value">{{ $invoice->recipient_company }}</div></div>
        <div class="inv-row"><div class="label">Телефон:</div><div class="value">{{ $invoice->recipient_phone }}</div></div>
        <div class="inv-row"><div class="label">Адрес:</div><div class="value">{{ $invoice->recipient_address }}, {{ $invoice->recipient_city }}, {{ $invoice->recipient_region }}, {{ $invoice->recipient_country }}</div></div>

        <div class="inv-section">Описание отправления</div>
        <div class="inv-row"><div class="label">Описание вложения:</div><div class="value">{{ $invoice->description }}</div></div>
        <div class="inv-row"><div class="label">Количество мест:</div><div class="value">{{ $invoice->quantity }}</div></div>
        <div class="inv-row"><div class="label">Вес (кг):</div><div class="value">{{ $invoice->weight }}</div></div>
        <div class="inv-row"><div class="label">Объёмный вес (кг):</div><div class="value">{{ $invoice->volume_weight }}</div></div>
        <div class="inv-row"><div class="label">Хрупкий груз:</div><div class="value">{{ $invoice->fragile ? 'Да' : 'Нет' }}</div></div>

        <div class="inv-section">Информация об оплате</div>
        <div class="inv-row"><div class="label">Объявленная ценность:</div><div class="value">{{ $invoice->declared_value }} KZT</div></div>
        @if($invoice->payment)
        <div class="inv-row"><div class="label">Сумма оплаты:</div><div class="value">{{ $invoice->payment }} KZT</div></div>
        @endif
        <div class="inv-row"><div class="label">Способ оплаты:</div><div class="value">
            @php
                $m = [];
                if ($invoice->payment_sender) $m[] = 'Оплата отправителем';
                if ($invoice->payment_recipient) $m[] = 'Оплата получателем';
                if ($invoice->payment_contract) $m[] = 'Оплата по договору';
                if ($invoice->payment_invoice) $m[] = 'Оплата по счету';
                if ($invoice->payment_cash) $m[] = 'Оплата наличными';
            @endphp
            {{ implode(', ', $m) ?: '—' }}
        </div></div>
        @if($invoice->special)
        <div class="inv-row"><div class="label">Особые инструкции:</div><div class="value">{{ $invoice->special }}</div></div>
        @endif
    </div>
</div>
@endsection
