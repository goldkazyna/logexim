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
    .inv-print-btn { display:inline-flex; align-items:center; gap:8px; background:#fff; color:#D0171C; border:1px solid #D0171C; border-radius:8px; padding:8px 18px; font-size:14px; font-weight:600; text-decoration:none; transition:.15s; }
    .inv-print-btn:hover { background:#D0171C; color:#fff; }
    .inv-save-btn { background: #D0171C; color: #fff; border: none; border-radius: 6px; padding: 8px 24px; font-size: 14px; font-weight: 600; cursor: pointer; margin-top: 20px; }
    .inv-save-btn:hover { background: #a01215; }
    .inv-success { color: #28a745; font-weight: 600; margin-left: 10px; display: none; }
    .inv-inline { display: inline-flex; align-items: center; gap: 8px; flex-wrap: wrap; }
    .inv-inline__input { width: 320px; max-width: 100%; }
    .inv-inline__btn { border: none; background: none; color: #999; cursor: pointer; padding: 2px 4px; font-size: 14px; }
    .inv-inline__btn:hover { color: #D0171C; }
    .inv-inline__save { color: #D0171C; font-size: 17px; }
    .inv-inline__err { color: #dc3545; font-size: 12px; font-weight: 400; }
    .inv-inline__form { display: inline-flex; flex-direction: column; gap: 4px; }
    .inv-inline__form[hidden] { display: none; }
    textarea.inv-inline__input { width: 420px; max-width: 100%; font-weight: 400; }
    .inv-inline__check { font-weight: 400; cursor: pointer; }
    .inv-inline__text { white-space: pre-line; }
    .inv-inline.is-saved .inv-inline__text { color: #28a745; transition: color .3s; }
</style>
@endpush
@section('content')
@php
    $canEdit = array_intersect($panelRoles, ['admin', 'dispatcher']) !== [];
@endphp
<div class="inv-back" style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px">
    <a href="/admin/invoices">&larr; Назад к списку</a>
    <a href="/admin/invoices/print/{{ $invoice->id }}" target="_blank" class="inv-print-btn">
        <i class="fas fa-print"></i> Печать / PDF
    </a>
</div>
<div class="card">
    <div class="card-header">Накладная № {{ $invoice->invoice_number }}</div>
    <div class="card-body">
        <div class="inv-row"><div class="label">Дата:</div><div class="value">
            @if($canEdit)
            <input type="date" name="date" class="inv-edit-input" value="{{ $invoice->date }}" form="edit-invoice-form" style="width:180px">
            @else
            {{ \Carbon\Carbon::parse($invoice->date)->format('d.m.Y') }}
            @endif
        </div></div>
        <div class="inv-row"><div class="label">Этап доставки:</div><div class="value">
            @if($canEdit)
            {{-- Шаги — по галочке «Без склада»; общий статус ставится сам по этапу. --}}
            <select name="detail_status" class="inv-edit-input" form="edit-invoice-form" style="width:280px">
                @foreach($invoice->stageOptions() as $k => $label)
                    <option value="{{ $k }}" @selected((int) $invoice->status !== 4 && $invoice->stageValue() === $k)>{{ $label }}</option>
                @endforeach
                <option value="cancel" @selected((int) $invoice->status === 4)>Отменена</option>
            </select>
            @else
            {{ (int) $invoice->status === 4 ? 'Отменена' : $invoice->stageTitle($invoice->effectiveStage()) }}
            @endif
        </div></div>
        <div class="inv-row"><div class="label">Без склада:</div><div class="value">
            @if($canEdit)
            <input type="hidden" name="same_city" value="0" form="edit-invoice-form">
            <label style="cursor:pointer"><input type="checkbox" name="same_city" value="1" form="edit-invoice-form" @checked($invoice->isLocalDelivery())>
                получатель в том же городе — короткий путь (заявка → курьер забрал → доставлено)</label>
            @else
            {{ $invoice->isLocalDelivery() ? 'Да — получатель в том же городе' : 'Нет' }}
            @endif
        </div></div>
        {{-- Курьеров заранее не назначают: кто отсканировал накладную, тот и
             закрепляется автоматически. Здесь показываем, кто уже взял. --}}
        <div class="inv-row"><div class="label">Курьер (отправка):</div><div class="value">{{ optional($invoice->courier)->full_name ?: '— заберёт любой курьер —' }}</div></div>
        <div class="inv-row"><div class="label">Курьер (приём в пункте):</div><div class="value">{{ optional($invoice->receivingCourier)->full_name ?: '— примет любой курьер —' }}</div></div>

        <div class="inv-section">Отправитель</div>
        @include('admin.partials.editable-field', ['field' => 'sender_name'])
        @include('admin.partials.editable-field', ['field' => 'sender_company'])
        @include('admin.partials.editable-field', ['field' => 'sender_phone'])
        @include('admin.partials.editable-field', ['field' => 'sender_city'])
        @include('admin.partials.editable-field', ['field' => 'sender_region'])
        @include('admin.partials.editable-field', ['field' => 'sender_district'])
        @include('admin.partials.editable-field', ['field' => 'sender_address'])

        <div class="inv-section">Получатель</div>
        @include('admin.partials.editable-field', ['field' => 'recipient_name'])
        @include('admin.partials.editable-field', ['field' => 'recipient_company'])
        @include('admin.partials.editable-field', ['field' => 'recipient_phone'])
        @include('admin.partials.editable-field', ['field' => 'recipient_city'])
        @include('admin.partials.editable-field', ['field' => 'recipient_region'])
        @include('admin.partials.editable-field', ['field' => 'recipient_district'])
        @include('admin.partials.editable-field', ['field' => 'recipient_address'])

        <div class="inv-section">Описание отправления</div>
        @foreach(['description', 'quantity', 'weight', 'volume_weight', 'fragile'] as $f)
        @include('admin.partials.editable-field', ['field' => $f])
        @endforeach

        @if(in_array('admin', $panelRoles, true))
        <div class="inv-section">Информация об оплате</div>
        @foreach(['declared_value', 'payment', 'payment_methods', 'special'] as $f)
        @include('admin.partials.editable-field', ['field' => $f])
        @endforeach
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
            @if($invoice->delivered_at)
                {{ \Carbon\Carbon::parse($invoice->delivered_at)->format('d.m.Y H:i') }}
            @elseif($invoice->fact_date)
                {{ \Carbon\Carbon::parse($invoice->fact_date)->format('d.m.Y') }}
            @else
                —
            @endif
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
                            $roleRu = match (true) {
                                $ev->actor_role === 'admin' => 'админ',
                                $ev->actor_role === 'client' => 'клиент',
                                isset(\App\Models\Staff::ROLE_LABELS[$ev->actor_role])
                                    => mb_strtolower(\App\Models\Staff::ROLE_LABELS[$ev->actor_role]),
                                default => null,
                            };
                        @endphp
                        {{ $actor }}@if($roleRu) ({{ $roleRu }})@endif
                        @if($ev->from_detail_status !== null && $ev->to_detail_status !== null)
                            · {{ $invoice->stageTitle((int) $ev->from_detail_status) }} → {{ $invoice->stageTitle((int) $ev->to_detail_status) }}@if($invoice->isLocalDelivery()) (без склада)@endif
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
                        @if(isset($ev->meta['field_label']))
                            · {{ $ev->meta['field_label'] }}: «{{ $ev->meta['from'] ?? '' }}» → «{{ $ev->meta['to'] ?? '' }}»
                        @endif
                        @if(isset($ev->meta['from_date']) && isset($ev->meta['to_date']))
                            · дата: {{ \Carbon\Carbon::parse($ev->meta['from_date'])->format('d.m.Y') }} → {{ \Carbon\Carbon::parse($ev->meta['to_date'])->format('d.m.Y') }}
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

@if($canEdit)
@push('scripts')
<script>
// Правка полей карточки на месте: карандаш → поле → дискетка.
// Enter — сохранить (в многострочном поле — Ctrl+Enter), Esc — отмена.
(function () {
    var url = '/admin/invoices/{{ $invoice->id }}/field';
    document.querySelectorAll('.inv-inline').forEach(function (box) {
        var type = box.dataset.type;
        var text = box.querySelector('.inv-inline__text');
        var form = box.querySelector('.inv-inline__form');
        var input = box.querySelector('.inv-inline__input');
        var checks = box.querySelectorAll('.inv-inline__check input');
        var edit = box.querySelector('.inv-inline__edit');
        var save = box.querySelector('.inv-inline__save');
        var err = box.querySelector('.inv-inline__err');

        // Снимок значения — чтобы Esc возвращал как было.
        function snapshot() {
            return type === 'methods'
                ? Array.prototype.map.call(checks, function (c) { return c.checked; })
                : input.value;
        }
        function restore(v) {
            if (type === 'methods') checks.forEach(function (c, k) { c.checked = v[k]; });
            else input.value = v;
        }
        function value() {
            if (type !== 'methods') return input.value;
            return Array.prototype.filter.call(checks, function (c) { return c.checked; })
                .map(function (c) { return c.value; });
        }
        var saved = snapshot();

        function open(on) {
            text.hidden = on; edit.hidden = on;
            form.hidden = !on; save.hidden = !on;
            err.hidden = true;
            if (on && input) { input.focus(); if (input.select) input.select(); }
        }
        edit.addEventListener('click', function () { open(true); });
        box.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') { restore(saved); open(false); }
            if (e.key === 'Enter' && (type !== 'textarea' || e.ctrlKey)) { e.preventDefault(); save.click(); }
        });
        save.addEventListener('click', function () {
            save.disabled = true;
            $.post(url, { field: box.dataset.field, value: value() })
                .done(function (res) {
                    if (type === 'methods') checks.forEach(function (c) { c.checked = res.value.indexOf(c.value) !== -1; });
                    else input.value = res.value;
                    saved = snapshot();
                    text.textContent = res.display;
                    open(false);
                    box.classList.add('is-saved');
                    setTimeout(function () { box.classList.remove('is-saved'); }, 1500);
                })
                .fail(function (xhr) {
                    err.textContent = (xhr.responseJSON && xhr.responseJSON.message) || 'Не удалось сохранить';
                    err.hidden = false;
                })
                .always(function () { save.disabled = false; });
        });
    });
})();
</script>
@endpush
@endif
