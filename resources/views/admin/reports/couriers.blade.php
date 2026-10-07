@extends('layouts.admin')
@section('title', 'Отчёт по курьерам')
@push('styles')
<style>
    .rep-filters { display: flex; flex-wrap: wrap; gap: 12px; align-items: flex-end; }
    .rep-presets { display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 14px; }
    .rep-preset { padding: 7px 14px; border: 1px solid #ddd; border-radius: 999px; background: #fff; color: #333; text-decoration: none; font-size: 13px; font-weight: 600; }
    .rep-preset:hover { border-color: #D0171C; color: #D0171C; }
    .rep-preset.is-active { background: #D0171C; border-color: #D0171C; color: #fff; }
    .rep-field { display: flex; flex-direction: column; gap: 4px; font-size: 12px; color: #888; }
    .rep-field input, .rep-field select { padding: 8px 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; background: #fff; min-width: 160px; }
    .rep-btn { padding: 9px 18px; border-radius: 8px; border: 1px solid #D0171C; background: #D0171C; color: #fff; font-size: 14px; font-weight: 600; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; }
    .rep-btn--ghost { background: #fff; color: #D0171C; }
    .rep-btn--ghost:hover { background: #D0171C; color: #fff; }

    .rep-kpis { display: grid; grid-template-columns: repeat(auto-fit, minmax(170px, 1fr)); gap: 12px; margin-bottom: 20px; }
    .rep-kpi { background: #fff; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,.08); padding: 14px 18px; }
    .rep-kpi__v { font-size: 26px; font-weight: 800; color: #1a1a1a; font-variant-numeric: tabular-nums; }
    .rep-kpi__t { font-size: 12px; color: #888; margin-top: 2px; }
    .rep-kpi--main .rep-kpi__v { color: #D0171C; }

    .rep-table td.num, .rep-table th.num { text-align: right; font-variant-numeric: tabular-nums; white-space: nowrap; }
    .rep-row { cursor: pointer; }
    .rep-row:hover td { background: #fafafa; }
    .rep-row td:first-child i { color: #bbb; width: 14px; transition: transform .15s; }
    .rep-row.is-open td:first-child i { transform: rotate(90deg); color: #D0171C; }
    .rep-detail td { background: #fbfbfb; padding: 0 !important; }
    .rep-detail table { margin: 0; }
    .rep-detail th, .rep-detail td { font-size: 13px; padding: 7px 12px !important; }
    .rep-tag { display: inline-block; padding: 2px 8px; border-radius: 999px; font-size: 11px; font-weight: 700; white-space: nowrap; }
    .rep-tag--pickup { background: #eef4ff; color: #2952cc; }
    .rep-tag--destination_pickup { background: #fff6e5; color: #a86400; }
    .rep-tag--delivery { background: #e8f7ee; color: #18794e; }
    .rep-total td { font-weight: 800; border-top: 2px solid #ddd; }
    .rep-empty { text-align: center; color: #888; padding: 30px !important; }
</style>
@endpush
@section('content')
@php
    $query = fn (array $extra = []) => http_build_query(array_filter([
        'preset' => $preset ?: null,
        'from' => $preset ? null : $from->format('Y-m-d'),
        'to' => $preset ? null : $to->format('Y-m-d'),
        'courier' => $courier,
    ] + $extra));
    $kg = fn ($v) => rtrim(rtrim(number_format($v, 2, ',', ' '), '0'), ',') ?: '0';
    $period = $from->equalTo($to) ? $from->format('d.m.Y') : $from->format('d.m.Y') . ' — ' . $to->format('d.m.Y');
@endphp

<div class="card">
    <div class="card-header">Отчёт по курьерам</div>
    <div class="card-body">
        <div class="rep-presets">
            @foreach($presets as $key => [$title])
                <a href="?{{ http_build_query(array_filter(['preset' => $key, 'courier' => $courier])) }}" class="rep-preset {{ $preset === $key ? 'is-active' : '' }}">{{ $title }}</a>
            @endforeach
        </div>
        <form method="get" class="rep-filters">
            <label class="rep-field">С
                <input type="date" name="from" value="{{ $from->format('Y-m-d') }}">
            </label>
            <label class="rep-field">По
                <input type="date" name="to" value="{{ $to->format('Y-m-d') }}">
            </label>
            <label class="rep-field">Курьер
                <select name="courier">
                    <option value="">Все курьеры</option>
                    @foreach($couriers as $c)
                        <option value="{{ $c->id }}" @selected($courier === $c->id)>{{ $c->full_name }}</option>
                    @endforeach
                </select>
            </label>
            <button type="submit" class="rep-btn"><i class="fas fa-filter"></i> Показать</button>
            <a href="/admin/reports/couriers/pdf?{{ $query() }}" class="rep-btn rep-btn--ghost"><i class="fas fa-file-pdf"></i> Скачать PDF</a>
            <a href="/admin/reports/couriers/pdf?{{ $query(['detailed' => '0']) }}" class="rep-btn rep-btn--ghost" title="Только итоговая таблица, без списка накладных">PDF — только итоги</a>
        </form>
    </div>
</div>

<div class="rep-kpis">
    <div class="rep-kpi rep-kpi--main"><div class="rep-kpi__v">{{ $totals['counts']['delivery'] }}</div><div class="rep-kpi__t">доставлено посылок</div></div>
    <div class="rep-kpi"><div class="rep-kpi__v">{{ $totals['counts']['pickup'] }}</div><div class="rep-kpi__t">забрано у отправителей</div></div>
    <div class="rep-kpi"><div class="rep-kpi__v">{{ $totals['counts']['destination_pickup'] }}</div><div class="rep-kpi__t">принято в пунктах назначения</div></div>
    <div class="rep-kpi"><div class="rep-kpi__v">{{ $kg($totals['delivered_weight']) }} кг</div><div class="rep-kpi__t">вес доставленного · {{ $totals['delivered_places'] }} мест</div></div>
</div>

<div class="card">
    <div class="card-header">{{ $period }}@if($courierName) · {{ $courierName }}@endif</div>
    <div class="card-body" style="overflow-x:auto">
        <table class="rep-table">
            <thead>
                <tr>
                    <th>Курьер</th>
                    <th class="num">Доставил</th>
                    <th class="num">Забрал</th>
                    <th class="num">Принял в пункте</th>
                    <th class="num">Вес доставленного</th>
                    <th class="num">Мест</th>
                </tr>
            </thead>
            <tbody>
            @forelse($rows as $i => $r)
                <tr class="rep-row" data-target="rep-d-{{ $i }}" title="Показать накладные">
                    <td><i class="fas fa-chevron-right"></i> {{ $r['name'] }}</td>
                    <td class="num"><b>{{ $r['counts']['delivery'] }}</b></td>
                    <td class="num">{{ $r['counts']['pickup'] }}</td>
                    <td class="num">{{ $r['counts']['destination_pickup'] }}</td>
                    <td class="num">{{ $kg($r['delivered_weight']) }} кг</td>
                    <td class="num">{{ $r['delivered_places'] }}</td>
                </tr>
                <tr class="rep-detail" id="rep-d-{{ $i }}" hidden>
                    <td colspan="6">
                        <table>
                            <thead><tr><th>Когда</th><th>Действие</th><th>Накладная</th><th>Маршрут</th><th>Получатель</th><th class="num">Вес</th><th class="num">Мест</th></tr></thead>
                            <tbody>
                            @foreach($r['items'] as $it)
                                <tr>
                                    <td style="white-space:nowrap">{{ $it['at']->format('d.m.Y H:i') }}</td>
                                    <td><span class="rep-tag rep-tag--{{ $it['action'] }}">{{ $it['action_label'] }}</span></td>
                                    <td><a href="/admin/invoices/view/{{ $it['invoice_id'] }}" target="_blank">№{{ $it['number'] }}</a></td>
                                    <td>{{ $it['from'] }} → {{ $it['to'] }}@if($it['local']) <small style="color:#888">(без склада)</small>@endif</td>
                                    <td>{{ $it['recipient'] }}</td>
                                    <td class="num">{{ $kg($it['weight']) }} кг</td>
                                    <td class="num">{{ $it['places'] }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="rep-empty">За этот период курьеры ничего не забирали и не доставляли</td></tr>
            @endforelse
            @if(count($rows) > 1)
                <tr class="rep-total">
                    <td>Итого</td>
                    <td class="num">{{ $totals['counts']['delivery'] }}</td>
                    <td class="num">{{ $totals['counts']['pickup'] }}</td>
                    <td class="num">{{ $totals['counts']['destination_pickup'] }}</td>
                    <td class="num">{{ $kg($totals['delivered_weight']) }} кг</td>
                    <td class="num">{{ $totals['delivered_places'] }}</td>
                </tr>
            @endif
            </tbody>
        </table>
    </div>
</div>
@endsection
@push('scripts')
<script>
// Клик по курьеру — раскрыть список его накладных.
document.querySelectorAll('.rep-row').forEach(function (row) {
    row.addEventListener('click', function () {
        var detail = document.getElementById(row.dataset.target);
        detail.hidden = !detail.hidden;
        row.classList.toggle('is-open', !detail.hidden);
    });
});
</script>
@endpush
