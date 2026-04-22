@php $showCourier = in_array(session('role'), ['admin', 'dispatcher'], true); @endphp
<table>
    <thead>
        <tr><th>№</th><th>Дата</th><th>ИИН/БИН</th><th>Отправитель</th><th>Получатель</th>@if($showCourier)<th>Курьер</th>@endif<th>Вес</th><th>Статус</th><th>Действие</th></tr>
    </thead>
    <tbody>
    @forelse($invoices as $inv)
    <tr>
        <td>{{ $inv->invoice_number }}</td>
        <td>{{ \Carbon\Carbon::parse($inv->created_at)->format('d.m.Y H:i') }}</td>
        <td>{{ $inv->user->bin ?? '—' }}</td>
        <td>{{ $inv->sender_company }}<br><small>{{ $inv->sender_name }}</small></td>
        <td>{{ $inv->recipient_company }}<br><small>{{ $inv->recipient_name }}</small></td>
        @if($showCourier)
        <td>{{ optional($inv->courier)->full_name ?: '—' }}</td>
        @endif
        <td>{{ $inv->weight }}</td>
        <td>
            <button type="button" class="status-badge s-{{ $inv->status }}" onclick="openStatusModal({{ $inv->id }}, {{ $inv->status }}, '{{ $inv->invoice_number }}')">
                @switch($inv->status)
                    @case(0) Заявка создана @break
                    @case(1) Принята в работу @break
                    @case(2) Отправлено @break
                    @case(3) Исполнена @break
                    @case(4) Отменена @break
                @endswitch
            </button>
        </td>
        <td><a href="/admin/invoices/view/{{ $inv->id }}" target="_blank" class="btn btn-sm btn-primary">Просмотр</a></td>
    </tr>
    @empty
    <tr><td colspan="{{ $showCourier ? 9 : 8 }}" style="text-align:center;padding:20px;color:#888">Накладные не найдены</td></tr>
    @endforelse
    </tbody>
</table>

@if($invoices->lastPage() > 1)
<div class="pagination-wrapper">
    <a href="{{ $invoices->previousPageUrl() ?? '#' }}" class="page-link {{ $invoices->onFirstPage() ? 'disabled' : '' }}">&laquo;</a>
    @php $current = $invoices->currentPage(); $last = $invoices->lastPage(); $start = max(1, $current - 3); $end = min($last, $current + 3); @endphp
    @if($start > 1)
        <a href="{{ $invoices->url(1) }}" class="page-link">1</a>
        @if($start > 2) <span class="page-link disabled">...</span> @endif
    @endif
    @for($i = $start; $i <= $end; $i++)
        <a href="{{ $invoices->url($i) }}" class="page-link {{ $i == $current ? 'active' : '' }}">{{ $i }}</a>
    @endfor
    @if($end < $last)
        @if($end < $last - 1) <span class="page-link disabled">...</span> @endif
        <a href="{{ $invoices->url($last) }}" class="page-link">{{ $last }}</a>
    @endif
    <a href="{{ $invoices->nextPageUrl() ?? '#' }}" class="page-link {{ $invoices->hasMorePages() ? '' : 'disabled' }}">&raquo;</a>
</div>
<div class="pagination-info">Показано {{ $invoices->firstItem() }}–{{ $invoices->lastItem() }} из {{ $invoices->total() }}</div>
@endif
