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
    .inv-edit-input { border: 1px solid #ccc; border-radius: 4px; padding: 4px 8px; font-size: 14px; font-weight: 600; width: 150px; }
    .inv-save-btn { background: #D0171C; color: #fff; border: none; border-radius: 6px; padding: 8px 24px; font-size: 14px; font-weight: 600; cursor: pointer; margin-top: 20px; }
    .inv-save-btn:hover { background: #a01215; }
    .inv-success { color: #28a745; font-weight: 600; margin-left: 10px; display: none; }
</style>
@endpush
@section('content')
@php
    $canEdit = in_array(session('role'), ['admin', 'dispatcher'], true);
    $statusLabels = [0=>'Заявка создана', 1=>'Принята в работу', 2=>'Отправлено', 3=>'Исполнена', 4=>'Отменена'];
@endphp
<div class="inv-back"><a href="/admin/invoices">&larr; Назад к списку</a></div>
<div class="card">
    <div class="card-header">Накладная № {{ $invoice->invoice_number }}</div>
    <div class="card-body">
        <div class="inv-row"><div class="label">Дата:</div><div class="value">{{ \Carbon\Carbon::parse($invoice->date)->format('d.m.Y') }}</div></div>
        <div class="inv-row"><div class="label">Статус:</div><div class="value">
            @if($canEdit)
            <select name="status" class="inv-edit-input" form="edit-invoice-form" style="width:200px">
                <option value="0" @if($invoice->status==0) selected @endif>Заявка создана</option>
                <option value="1" @if($invoice->status==1) selected @endif>Принята в работу</option>
                <option value="2" @if($invoice->status==2) selected @endif>Отправлено</option>
                <option value="3" @if($invoice->status==3) selected @endif>Исполнена</option>
                <option value="4" @if($invoice->status==4) selected @endif>Отменена</option>
            </select>
            @else
            {{ $statusLabels[$invoice->status] ?? '—' }}
            @endif
        </div></div>
        <div class="inv-row"><div class="label">Этап доставки:</div><div class="value">
            @if($canEdit)
            <select name="detail_status" class="inv-edit-input" form="edit-invoice-form" style="width:280px">
                @foreach(\App\Models\Invoice::DETAIL_STATUSES as $k => $label)
                    <option value="{{ $k }}" @if((int)$invoice->detail_status === $k) selected @endif>{{ $label }}</option>
                @endforeach
            </select>
            @else
            {{ $invoice->detailStatusLabel() }}
            @endif
        </div></div>
        @if($canEdit)
        <div class="inv-row"><div class="label">Курьер (отправка):</div><div class="value">
            <select name="courier_id" class="inv-edit-input" form="edit-invoice-form" style="width:260px">
                <option value="">— не назначен —</option>
                @foreach($couriers as $c)
                    <option value="{{ $c->id }}" @if($invoice->courier_id == $c->id) selected @endif>{{ $c->full_name }}@if(!$c->active) (отключен)@endif</option>
                @endforeach
            </select>
        </div></div>
        <div class="inv-row"><div class="label">Курьер (приём в пункте):</div><div class="value">
            <select name="receiving_courier_id" class="inv-edit-input" form="edit-invoice-form" style="width:260px">
                <option value="">— не назначен —</option>
                @foreach($receivingCouriers as $c)
                    <option value="{{ $c->id }}" @if($invoice->receiving_courier_id == $c->id) selected @endif>{{ $c->full_name }}@if(!$c->active) (отключен)@endif</option>
                @endforeach
            </select>
        </div></div>
        @else
        <div class="inv-row"><div class="label">Курьер (отправка):</div><div class="value">{{ optional($invoice->courier)->full_name ?: '—' }}</div></div>
        <div class="inv-row"><div class="label">Курьер (приём в пункте):</div><div class="value">{{ optional($invoice->receivingCourier)->full_name ?: '—' }}</div></div>
        @endif

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
        <div class="inv-row"><div class="label">Объёмный вес (кг):</div><div class="value">
            @if($canEdit)
            <input type="number" step="0.01" name="volume_weight" class="inv-edit-input" value="{{ $invoice->volume_weight }}" form="edit-invoice-form">
            @else
            {{ $invoice->volume_weight }}
            @endif
        </div></div>
        <div class="inv-row"><div class="label">Хрупкий груз:</div><div class="value">{{ $invoice->fragile ? 'Да' : 'Нет' }}</div></div>

        @if(session('role') === 'admin')
        <div class="inv-section">Информация об оплате</div>
        <div class="inv-row"><div class="label">Объявленная ценность:</div><div class="value">{{ $invoice->declared_value }} KZT</div></div>
        <div class="inv-row"><div class="label">Сумма оплаты (KZT):</div><div class="value"><input type="number" step="0.01" name="payment" class="inv-edit-input" value="{{ $invoice->payment }}" form="edit-invoice-form"></div></div>
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
        @endif

        @if($invoice->pickup_signature)
        <div class="inv-section">Забор курьером</div>
        <div class="inv-row"><div class="label">Курьер:</div><div class="value">{{ optional($invoice->courier)->full_name ?: '—' }}</div></div>
        <div class="inv-row"><div class="label">Подпись отправителя:</div><div class="value">
            <a href="/storage/{{ $invoice->pickup_signature }}" target="_blank" style="display:inline-block">
                <img src="/storage/{{ $invoice->pickup_signature }}" alt="Подпись отправителя"
                     style="max-width:320px;max-height:200px;border:1px solid #e5e7eb;border-radius:8px;background:#fff;padding:6px">
            </a>
        </div></div>
        @endif

        @if($invoice->delivery_signature || $invoice->delivered_at)
        <div class="inv-section">Доставка получателю</div>
        <div class="inv-row"><div class="label">Курьер-получатель:</div><div class="value">{{ optional($invoice->receivingCourier)->full_name ?: '—' }}</div></div>
        @if($invoice->delivered_at)
        <div class="inv-row"><div class="label">Дата доставки:</div><div class="value">{{ \Carbon\Carbon::parse($invoice->delivered_at)->format('d.m.Y H:i') }}</div></div>
        @endif
        @if($invoice->delivery_signature)
        <div class="inv-row"><div class="label">Подпись получателя:</div><div class="value">
            <a href="/storage/{{ $invoice->delivery_signature }}" target="_blank" style="display:inline-block">
                <img src="/storage/{{ $invoice->delivery_signature }}" alt="Подпись получателя"
                     style="max-width:320px;max-height:200px;border:1px solid #e5e7eb;border-radius:8px;background:#fff;padding:6px">
            </a>
        </div></div>
        @endif
        @endif

        <div class="inv-section">Доставка</div>
        <div class="inv-row"><div class="label">Доставка по договору:</div><div class="value">
            @if($canEdit)
            <input type="date" name="plan_date" class="inv-edit-input" value="{{ $invoice->plan_date }}" form="edit-invoice-form" style="width:180px">
            @else
            {{ $invoice->plan_date ?: '—' }}
            @endif
        </div></div>
        <div class="inv-row"><div class="label">Фактическая доставка:</div><div class="value">
            @if($canEdit)
            <input type="date" name="fact_date" class="inv-edit-input" value="{{ $invoice->fact_date }}" form="edit-invoice-form" style="width:180px">
            @else
            {{ $invoice->fact_date ?: '—' }}
            @endif
        </div></div>

        @if($invoice->events && count($invoice->events))
        <div class="inv-section">История накладной</div>
        <div style="background:#f8f9fa;border-radius:10px;padding:14px 18px;margin-bottom:14px">
            @foreach($invoice->events as $ev)
            <div style="display:flex;gap:14px;padding:8px 0;{{ !$loop->last ? 'border-bottom:1px solid #eee' : '' }}">
                <div style="width:140px;font-size:12px;color:#888">
                    {{ $ev->created_at ? \Carbon\Carbon::parse($ev->created_at)->format('d.m.Y H:i') : '—' }}
                </div>
                <div style="flex:1">
                    <div style="font-weight:600;font-size:14px;color:#1a1a1a">{{ $ev->label() }}</div>
                    <div style="font-size:12px;color:#666;margin-top:2px">
                        @php
                            $actor = $ev->actor_name ?: ($ev->actor_role ?: '—');
                            $roleRu = [
                                'admin' => 'админ',
                                'dispatcher' => 'диспетчер',
                                'courier' => 'курьер',
                                'warehouse' => 'кладовщик',
                            ][$ev->actor_role] ?? null;
                        @endphp
                        {{ $actor }}@if($roleRu) ({{ $roleRu }})@endif
                        @if($ev->from_detail_status !== null && $ev->to_detail_status !== null)
                            · {{ \App\Models\Invoice::DETAIL_STATUSES[$ev->from_detail_status] ?? '?' }} → {{ \App\Models\Invoice::DETAIL_STATUSES[$ev->to_detail_status] ?? '?' }}
                        @endif
                        @if(isset($ev->meta['courier_name']))
                            · назначен курьер: {{ $ev->meta['courier_name'] }}
                        @endif
                        @if(isset($ev->meta['warehouse_location']))
                            · склад: {{ $ev->meta['warehouse_location'] }}
                        @endif
                        @if(isset($ev->meta['from_status']) && isset($ev->meta['to_status']))
                            @php
                                $statuses = [0=>'Заявка создана',1=>'Принята в работу',2=>'Отправлено',3=>'Исполнена',4=>'Отменена'];
                            @endphp
                            · статус: {{ $statuses[$ev->meta['from_status']] ?? '?' }} → {{ $statuses[$ev->meta['to_status']] ?? '?' }}
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif

        @if($canEdit)
        <form id="edit-invoice-form" action="/admin/invoices/update/{{ $invoice->id }}" method="post">
            @csrf
            <button type="submit" class="inv-save-btn">Сохранить изменения</button>
        </form>
        @if(session('success'))
        <span class="inv-success" style="display:inline">{{ session('success') }}</span>
        @endif
        @endif
    </div>
</div>
@endsection
