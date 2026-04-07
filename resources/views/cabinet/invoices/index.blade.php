@extends('layouts.cabinet')
@section('title', 'Накладные')
@push('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
<style>
    .common_btn { background-color: #D0171C; border-color: #D0171C; border-radius: 10px; color: #ffffff; padding: 8px 20px; border: none; cursor: pointer; text-decoration: none; display: inline-block; }
    .common_btn:hover { background-color: #a21216; }
    .icon-btn { font-size: 20px; cursor: pointer; color: #D0171C; }
    .icon-btn:hover { color: #a21216; }
    .search-input { padding: 10px 15px; border: 1px solid #ddd; border-radius: 10px; font-size: 14px; width: 300px; outline: none; transition: border-color 0.3s; }
    .search-input:focus { border-color: #D0171C; }
    .search-wrapper { display: flex; align-items: center; gap: 10px; margin-bottom: 15px; }
    .search-wrapper i { color: #999; font-size: 18px; }
</style>
@endpush
@section('content')
<main class="flex-grow p-6">
    <div class="flex items-center justify-between flex-wrap gap-2 mb-6">
        <div style="width: 100%;">
            <div class="card overflow-hidden">
                <div class="card-header">
                    <h4 class="text-slate-900 text-lg font-medium mb-2">Накладные</h4>
                </div>
                <div>
                    <div class="overflow-x-auto">
                        <div class="min-w-full inline-block align-middle">
                            <div class="overflow-hidden" style="padding: 20px;">
                                <p><b>Накладная</b> — это документ для отправки товаров. В этом разделе вы можете просмотреть свои накладные и управлять ими.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                @if(session('success_message'))
                <div id="success-alert" class="bg-primary text-sm text-white rounded-md p-4 mb-4" role="alert">
                    <span class="font-bold">Успешно!</span> {{ session('success_message') }}
                </div>
                @endif
            </div>

            <div class="mt-4" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                    <a href="{{ url('cabinet/create_invoice') }}" class="btn btn-primary common_btn">Добавить накладную</a>
                    @if($unprintedCount > 0)
                    <a href="{{ url('cabinet/invoices?unprinted=1') }}" class="btn common_btn" id="unprinted-btn" style="background-color: {{ request('unprinted') == '1' ? '#a21216' : '#ff6600' }};">
                        <i class="fas fa-print"></i> Ненапечатанные накладные ({{ $unprintedCount }})
                    </a>
                    @endif
                    @if(request('unprinted') == '1')
                    <a href="{{ url('cabinet/invoices') }}" class="btn common_btn" style="background-color: #6c757d;">Показать все</a>
                    @endif
                </div>
                <div class="search-wrapper">
                    <i class="fas fa-search"></i>
                    <input type="text" id="invoice-search" class="search-input" placeholder="Поиск по номеру, отправителю, получателю..." autocomplete="off">
                </div>
            </div>
            <br />

            <div class="card overflow-hidden">
                <div class="card-header">
                    <h4 class="card-title">Список накладных</h4>
                </div>
                <div>
                    <div class="overflow-x-auto">
                        <div class="min-w-full inline-block align-middle">
                            <div class="overflow-hidden" id="invoices-table">
                                @include('cabinet.invoices._table')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection
@push('scripts')
<script>
$(document).ready(function() {
    setTimeout(function() { $('#success-alert').fadeOut('slow'); }, 5000);

    var searchTimer;
    $('#invoice-search').on('input', function() {
        var query = $(this).val();
        clearTimeout(searchTimer);
        searchTimer = setTimeout(function() {
            $.ajax({
                url: '/cabinet/invoices',
                type: 'GET',
                data: { search: query },
                success: function(html) {
                    $('#invoices-table').html(html);
                }
            });
        }, 300);
    });

    // Делегирование кликов по пагинации внутри AJAX-контента
    $(document).on('click', '#invoices-table .pagination a', function(e) {
        e.preventDefault();
        var url = $(this).attr('href');
        var search = $('#invoice-search').val();
        if (search) {
            url += (url.indexOf('?') !== -1 ? '&' : '?') + 'search=' + encodeURIComponent(search);
        }
        $.ajax({
            url: url,
            success: function(html) {
                $('#invoices-table').html(html);
            }
        });
    });
});
</script>
@endpush
