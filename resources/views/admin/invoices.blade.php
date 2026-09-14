@extends('layouts.admin')
@section('title', 'Накладные')
@push('styles')
<style>
    /* Полоса прогресса этапа доставки */
    .stage-prog__dots { display: flex; align-items: center; gap: 4px; }
    .stage-prog__dot { width: 10px; height: 10px; border-radius: 50%; background: #e2e5ea; display: block; }
    .stage-prog__dot.done { background: #16a34a; }
    .stage-prog__dot.cur { background: #d0171c; box-shadow: 0 0 0 3px rgba(208,23,28,.18); }
    .stage-prog__label { font-size: 12px; color: #555; margin-top: 5px; }
    .stage-cancelled { display: inline-block; font-size: 12px; font-weight: 600; color: #dc3545; background: #fdecec; padding: 4px 12px; border-radius: 999px; }

    .pagination-wrapper { display: flex; align-items: center; justify-content: center; gap: 4px; margin-top: 20px; flex-wrap: wrap; }
    .pagination-wrapper .page-link { display: inline-flex; align-items: center; justify-content: center; min-width: 36px; height: 36px; padding: 0 10px; border: 1px solid #ddd; border-radius: 6px; text-decoration: none; color: #333; font-size: 14px; background: #fff; transition: all 0.2s; }
    .pagination-wrapper .page-link:hover { background: #f0f0f0; }
    .pagination-wrapper .page-link.active { background: #D0171C; color: #fff; border-color: #D0171C; }
    .pagination-wrapper .page-link.disabled { color: #ccc; pointer-events: none; }
    .pagination-info { text-align: center; font-size: 13px; color: #888; margin-top: 8px; }
</style>
@endpush
@section('content')
<div style="margin-bottom:15px;display:flex;align-items:center;gap:10px;flex-wrap:wrap">
    <i class="fas fa-search" style="color:#999;font-size:18px"></i>
    <input type="text" id="invoice-search" placeholder="Поиск по номеру, отправителю, получателю..." style="padding:10px 15px;border:1px solid #ddd;border-radius:10px;font-size:14px;width:350px;outline:none" autocomplete="off">
    <select id="bin-filter" style="padding:10px 15px;border:1px solid #ddd;border-radius:10px;font-size:14px;outline:none;min-width:200px;background:#fff">
        <option value="">Все ИИН/БИН</option>
        @foreach($bins as $id => $bin)
            <option value="{{ $bin }}">{{ $bin }}</option>
        @endforeach
    </select>
</div>
<div class="card" id="invoices-card">
    <div class="card-header">Все накладные</div>
    <div class="card-body">
        <div id="invoices-table">
            @include('admin.invoices_table')
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
function loadInvoices() {
    var data = {};
    var search = $('#invoice-search').val();
    var bin = $('#bin-filter').val();
    if (search) data.search = search;
    if (bin) data.bin = bin;
    $.ajax({
        url: '/admin/invoices',
        type: 'GET',
        data: data,
        success: function(html) { $('#invoices-table').html(html); }
    });
}

var searchTimer;
$('#invoice-search').on('input', function() {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(loadInvoices, 300);
});

$('#bin-filter').on('change', function() {
    loadInvoices();
});

$(document).on('click', '#invoices-table .pagination-wrapper .page-link:not(.disabled)', function(e) {
    e.preventDefault();
    var url = $(this).attr('href');
    if (url === '#') return;
    var search = $('#invoice-search').val();
    var bin = $('#bin-filter').val();
    if (search) url += (url.indexOf('?') !== -1 ? '&' : '?') + 'search=' + encodeURIComponent(search);
    if (bin) url += (url.indexOf('?') !== -1 ? '&' : '?') + 'bin=' + encodeURIComponent(bin);
    $.ajax({ url: url, success: function(html) { $('#invoices-table').html(html); } });
});
</script>
@endpush
