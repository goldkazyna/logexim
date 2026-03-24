@extends('layouts.admin')
@section('title', 'Накладные')
@push('styles')
<style>
    .status-badge { padding: 6px 14px; border-radius: 20px; font-size: 12px; color: #fff; font-weight: 600; display: inline-block; min-width: 130px; text-align: center; cursor: pointer; border: none; transition: opacity 0.2s; }
    .status-badge:hover { opacity: 0.85; }
    .s-0 { background: #00056d; }
    .s-1 { background: #ffcc00; color: #333; }
    .s-2 { background: #00aaff; }
    .s-3 { background: #28a745; }
    .s-4 { background: #dc3545; }

    .modal-overlay { display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 9999; justify-content: center; align-items: center; }
    .modal-overlay.active { display: flex; }
    .modal-box { background: #fff; border-radius: 12px; padding: 25px; width: 360px; box-shadow: 0 15px 40px rgba(0,0,0,0.2); }
    .modal-box h3 { margin: 0 0 8px; font-size: 18px; }
    .modal-box .modal-subtitle { color: #888; font-size: 13px; margin-bottom: 20px; }
    .modal-box .status-option { display: block; width: 100%; padding: 12px 16px; margin-bottom: 8px; border-radius: 8px; border: 2px solid #eee; background: #fff; cursor: pointer; font-size: 14px; font-weight: 600; text-align: left; transition: all 0.15s; }
    .modal-box .status-option:hover { border-color: #D0171C; transform: scale(1.02); }
    .modal-box .status-option.current { border-color: #D0171C; background: #fff5f5; }
    .modal-box .status-option .dot { display: inline-block; width: 10px; height: 10px; border-radius: 50%; margin-right: 10px; }
    .modal-box .btn-close-modal { margin-top: 10px; width: 100%; padding: 10px; background: #f0f0f0; border: none; border-radius: 8px; cursor: pointer; font-size: 14px; color: #666; }
    .modal-box .btn-close-modal:hover { background: #e0e0e0; }

    .pagination-wrapper { display: flex; align-items: center; justify-content: center; gap: 4px; margin-top: 20px; flex-wrap: wrap; }
    .pagination-wrapper .page-link { display: inline-flex; align-items: center; justify-content: center; min-width: 36px; height: 36px; padding: 0 10px; border: 1px solid #ddd; border-radius: 6px; text-decoration: none; color: #333; font-size: 14px; background: #fff; transition: all 0.2s; }
    .pagination-wrapper .page-link:hover { background: #f0f0f0; }
    .pagination-wrapper .page-link.active { background: #D0171C; color: #fff; border-color: #D0171C; }
    .pagination-wrapper .page-link.disabled { color: #ccc; pointer-events: none; }
    .pagination-info { text-align: center; font-size: 13px; color: #888; margin-top: 8px; }
</style>
@endpush
@section('content')
<div style="margin-bottom:15px;display:flex;align-items:center;gap:10px">
    <i class="fas fa-search" style="color:#999;font-size:18px"></i>
    <input type="text" id="invoice-search" placeholder="Поиск по номеру накладной..." style="padding:10px 15px;border:1px solid #ddd;border-radius:10px;font-size:14px;width:350px;outline:none" autocomplete="off">
</div>
<div class="card" id="invoices-card">
    <div class="card-header">Все накладные</div>
    <div class="card-body">
        <div id="invoices-table">
            @include('admin.invoices_table')
        </div>
    </div>
</div>

<!-- Модалка смены статуса -->
<div class="modal-overlay" id="statusModal">
    <div class="modal-box">
        <h3>Изменить статус</h3>
        <div class="modal-subtitle">Накладная №<span id="modal-inv-num"></span></div>
        <form id="statusForm" method="post">
            @csrf
            <button type="submit" name="status" value="0" class="status-option" data-status="0"><span class="dot" style="background:#00056d"></span>Заявка создана</button>
            <button type="submit" name="status" value="1" class="status-option" data-status="1"><span class="dot" style="background:#ffcc00"></span>Принята в работу</button>
            <button type="submit" name="status" value="2" class="status-option" data-status="2"><span class="dot" style="background:#00aaff"></span>Отправлено</button>
            <button type="submit" name="status" value="3" class="status-option" data-status="3"><span class="dot" style="background:#28a745"></span>Исполнена</button>
            <button type="submit" name="status" value="4" class="status-option" data-status="4"><span class="dot" style="background:#dc3545"></span>Отменена</button>
        </form>
        <button class="btn-close-modal" onclick="closeStatusModal()">Отмена</button>
    </div>
</div>
@endsection
@push('scripts')
<script>
function openStatusModal(id, currentStatus, invNum) {
    document.getElementById('statusForm').action = '/admin/invoices/status/' + id;
    document.getElementById('modal-inv-num').textContent = invNum;
    document.querySelectorAll('.status-option').forEach(function(btn) {
        btn.classList.remove('current');
        if (parseInt(btn.dataset.status) === currentStatus) btn.classList.add('current');
    });
    document.getElementById('statusModal').classList.add('active');
}
function closeStatusModal() {
    document.getElementById('statusModal').classList.remove('active');
}
document.getElementById('statusModal').addEventListener('click', function(e) {
    if (e.target === this) closeStatusModal();
});

var searchTimer;
$('#invoice-search').on('input', function() {
    var query = $(this).val();
    clearTimeout(searchTimer);
    searchTimer = setTimeout(function() {
        $.ajax({
            url: '/admin/invoices',
            type: 'GET',
            data: { search: query },
            success: function(html) { $('#invoices-table').html(html); }
        });
    }, 300);
});

$(document).on('click', '#invoices-table .pagination-wrapper .page-link:not(.disabled)', function(e) {
    e.preventDefault();
    var url = $(this).attr('href');
    if (url === '#') return;
    var search = $('#invoice-search').val();
    if (search) url += (url.indexOf('?') !== -1 ? '&' : '?') + 'search=' + encodeURIComponent(search);
    $.ajax({ url: url, success: function(html) { $('#invoices-table').html(html); } });
});
</script>
@endpush
