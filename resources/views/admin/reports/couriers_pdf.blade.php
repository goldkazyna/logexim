<!DOCTYPE html>
<html lang="ru">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
@php
    $kg = fn ($v) => rtrim(rtrim(number_format($v, 2, ',', ' '), '0'), ',') ?: '0';
    $period = $from->equalTo($to) ? $from->format('d.m.Y') : $from->format('d.m.Y') . ' — ' . $to->format('d.m.Y');
@endphp
<title>Отчёт по курьерам {{ $period }}</title>
<style>
    * { font-family: 'DejaVu Sans', sans-serif; }
    body { font-size: 10px; color: #1a1a1a; margin: 0; }
    h1 { font-size: 16px; margin: 0 0 2px; }
    .sub { color: #666; font-size: 10px; margin-bottom: 12px; }
    .brand { color: #D0171C; font-weight: bold; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
    th, td { border: 1px solid #ccc; padding: 4px 6px; text-align: left; vertical-align: top; }
    th { background: #f2f2f2; font-size: 9px; }
    .num { text-align: right; white-space: nowrap; }
    .total td { font-weight: bold; background: #fafafa; }
    .kpis td { border: none; padding: 0 10px 0 0; }
    .kpi { border: 1px solid #ddd; padding: 6px 8px; }
    .kpi b { font-size: 15px; display: block; }
    .kpi span { color: #666; font-size: 9px; }
    h2 { font-size: 12px; margin: 14px 0 4px; }
    .detail th, .detail td { font-size: 9px; padding: 3px 5px; }
    .muted { color: #888; }
</style>
</head>
<body>
    <h1>Отчёт по курьерам</h1>
    <div class="sub"><span class="brand">LogExim Express</span> · период: {{ $period }}@if($courierName) · курьер: {{ $courierName }}@endif · сформирован {{ now()->format('d.m.Y H:i') }}</div>

    <table class="kpis"><tr>
        <td><div class="kpi"><b>{{ $totals['counts']['delivery'] }}</b><span>доставлено посылок</span></div></td>
        <td><div class="kpi"><b>{{ $totals['counts']['pickup'] }}</b><span>забрано у отправителей</span></div></td>
        <td><div class="kpi"><b>{{ $totals['counts']['destination_pickup'] }}</b><span>принято в пунктах</span></div></td>
        <td><div class="kpi"><b>{{ $kg($totals['delivered_weight']) }} кг</b><span>вес доставленного · {{ $totals['delivered_places'] }} мест</span></div></td>
    </tr></table>

    <table>
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
        @forelse($rows as $r)
            <tr>
                <td>{{ $r['name'] }}</td>
                <td class="num"><b>{{ $r['counts']['delivery'] }}</b></td>
                <td class="num">{{ $r['counts']['pickup'] }}</td>
                <td class="num">{{ $r['counts']['destination_pickup'] }}</td>
                <td class="num">{{ $kg($r['delivered_weight']) }} кг</td>
                <td class="num">{{ $r['delivered_places'] }}</td>
            </tr>
        @empty
            <tr><td colspan="6" class="muted" style="text-align:center">За этот период курьеры ничего не забирали и не доставляли</td></tr>
        @endforelse
        @if(count($rows) > 1)
            <tr class="total">
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

    @if($detailed)
        @foreach($rows as $r)
            <h2>{{ $r['name'] }} — доставил {{ $r['counts']['delivery'] }}, забрал {{ $r['counts']['pickup'] }}@if($r['counts']['destination_pickup']), принял в пункте {{ $r['counts']['destination_pickup'] }}@endif</h2>
            <table class="detail">
                <thead><tr><th>Когда</th><th>Действие</th><th>Накладная</th><th>Маршрут</th><th>Получатель</th><th class="num">Вес</th><th class="num">Мест</th></tr></thead>
                <tbody>
                @foreach($r['items'] as $it)
                    <tr>
                        <td style="white-space:nowrap">{{ $it['at']->format('d.m.Y H:i') }}</td>
                        <td>{{ \App\Support\CourierReport::SHORT[$it['action']] }}</td>
                        <td>№{{ $it['number'] }}</td>
                        <td>{{ $it['from'] }} → {{ $it['to'] }}@if($it['local']) <span class="muted">(без склада)</span>@endif</td>
                        <td>{{ $it['recipient'] }}</td>
                        <td class="num">{{ $kg($it['weight']) }} кг</td>
                        <td class="num">{{ $it['places'] }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endforeach
    @endif
</body>
</html>
