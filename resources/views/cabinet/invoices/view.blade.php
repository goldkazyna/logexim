@extends('layouts.cabinet')
@section('title', 'Просмотр накладной')
@push('styles')
<style>
.common_btn { background-color: #D0171C; border-color: #D0171C; border-radius: 10px; color:#ffffff; }
.common_btn:hover { background-color: #a21216; }
h2 { background-color: #D0171C; color: #ffffff !important; text-align: center; border-radius: 10px; padding:5px; margin-bottom:10px; }
.invoice-data { font-size: 16px; font-weight: bold; color: #333; margin-bottom: 20px; }
.invoice-data span { display: inline-block; width: 200px; color: #555; font-weight: normal; }
.date-form { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
.date-form input[type="date"] { border: 1px solid #ccc; border-radius: 8px; padding: 6px 10px;
                                font-size: 15px; font-weight: bold; color: #333; }
.date-save { padding: 6px 16px; font-size: 14px; border: none; cursor: pointer; }
.date-msg { border-radius: 8px; padding: 8px 12px; margin-bottom: 12px; font-size: 14px; }
.date-ok  { background: #e8f7ed; color: #1c7a3e; }
.date-err { background: #fdecea; color: #a21216; }
</style>
@endpush
@section('content')
<main class="flex-grow p-6">
    <div class="flex items-center justify-between flex-wrap gap-2 mb-6">
        <div>
            <h4 class="text-slate-900 text-lg font-medium mb-2">Просмотр накладной № {{ $invoice->invoice_number }}</h4>
            <div class="md:flex hidden items-center gap-3 text-sm font-semibold">
                <p>Полная информация о накладной</p>
            </div>

            <div class="mt-4">
                <!-- Дата создания — клиент может её поправить -->
                @if(session('success'))
                    <div class="date-msg date-ok">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="date-msg date-err">{{ session('error') }}</div>
                @endif
                <form action="/cabinet/update_invoice_date/{{ $invoice->id }}" method="POST" class="invoice-data date-form">
                    @csrf
                    <span>Дата:</span>
                    <input type="date" name="date" value="{{ $invoice->date }}" required>
                    <button type="submit" class="common_btn date-save">Сохранить</button>
                </form>

                <!-- Отправитель -->
                <h2>Отправитель</h2>
                <div class="invoice-data">
                    <span>ФИО отправителя:</span> {{ $invoice->sender_name }}
                </div>
                <div class="invoice-data">
                    <span>Компания:</span> {{ $invoice->sender_company }}
                </div>
                <div class="invoice-data">
                    <span>Телефон:</span> {{ $invoice->sender_phone }}
                </div>
                <div class="invoice-data">
                    <span>Адрес:</span> {{ $invoice->sender_address }}, {{ $invoice->sender_city }}, {{ $invoice->sender_region }}, {{ $invoice->sender_country }}
                </div>

                <!-- Получатель -->
                <h2>Получатель</h2>
                <div class="invoice-data">
                    <span>ФИО получателя:</span> {{ $invoice->recipient_name }}
                </div>
                <div class="invoice-data">
                    <span>Компания:</span> {{ $invoice->recipient_company }}
                </div>
                <div class="invoice-data">
                    <span>Телефон:</span> {{ $invoice->recipient_phone }}
                </div>
                <div class="invoice-data">
                    <span>Адрес:</span> {{ $invoice->recipient_address }}, {{ $invoice->recipient_city }}, {{ $invoice->recipient_region }}, {{ $invoice->recipient_country }}
                </div>

                <!-- Описание отправления -->
                <h2>Описание отправления</h2>
                <div class="invoice-data">
                    <span>Описание вложения:</span> {{ $invoice->description }}
                </div>
                <div class="invoice-data">
                    <span>Количество мест:</span> {{ $invoice->quantity }}
                </div>
                <div class="invoice-data">
                    <span>Вес (кг):</span> {{ $invoice->weight }}
                </div>
                <div class="invoice-data">
                    <span>Объёмный вес (кг):</span> {{ $invoice->volume_weight }}
                </div>
                <div class="invoice-data">
                    <span>Хрупкий груз:</span> {{ $invoice->fragile ? 'Да' : 'Нет' }}
                </div>

                <!-- Информация об оплате -->
                <h2>Информация об оплате</h2>
                <div class="invoice-data">
                    <span>Объявленная ценность (в KZT):</span> {{ $invoice->declared_value }}
                </div>
                <div class="invoice-data">
                    <span>Сумма оплаты (в KZT):</span> {{ $invoice->payment }}
                </div>
                <div class="invoice-data">
                    <span>Способ оплаты:</span>
                    @php
                        $methods = [];
                        if ($invoice->payment_sender) $methods[] = 'Оплата отправителем';
                        if ($invoice->payment_recipient) $methods[] = 'Оплата получателем';
                        if ($invoice->payment_contract) $methods[] = 'Оплата по договору';
                        if ($invoice->payment_invoice) $methods[] = 'Оплата по счету';
                        if ($invoice->payment_cash) $methods[] = 'Оплата наличными';
                    @endphp
                    {{ implode(', ', $methods) }}
                </div>

                <!-- Статус накладной -->
                <div class="invoice-data">
                    <span>Статус:</span>
                    @switch($invoice->status)
                        @case(0) Заявка создана @break
                        @case(1) Принята в работу @break
                        @case(2) Отправлено @break
                        @case(3) Исполнено @break
                        @default Неизвестный статус
                    @endswitch
                </div>
                <div class="invoice-data">
                    <span>Особые инструкции:</span> {{ $invoice->special }}
                </div>
            </div>
        </div>
    </div>
</main>
@endsection
