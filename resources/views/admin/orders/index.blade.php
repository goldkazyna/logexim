@extends('layouts.admin')
@section('title', 'Заказы/Трекинг')
@section('content')
<a href="/admin/orders/create" class="btn btn-primary" style="margin-bottom:15px">Добавить заказ</a>
<div class="card">
    <div class="card-header">Все заказы</div>
    <div class="card-body">
        <table>
            <tr><th>ID</th><th>Трек-номер</th><th>Отправитель</th><th>Получатель</th><th>Дата отправки</th><th>Дата доставки</th><th>Статус</th><th>Действие</th></tr>
            @foreach($orders as $o)
            <tr>
                <td>{{ $o->id }}</td>
                <td>{{ $o->track_number }}</td>
                <td>{{ $o->name_from }}</td>
                <td>{{ $o->name_to }}</td>
                <td>{{ $o->date_from }}</td>
                <td>{{ $o->date_to }}</td>
                <td>{!! $o->status==1 ? '<span class="badge" style="background:#28a745">Доставлен</span>' : '<span class="badge" style="background:#007bff">В пути</span>' !!}</td>
                <td>
                    <a href="/admin/orders/edit/{{ $o->id }}" class="btn btn-sm btn-primary">Ред.</a>
                    <a href="/admin/orders/delete/{{ $o->id }}" class="btn btn-sm btn-danger" onclick="return confirm('Удалить?')">Уд.</a>
                </td>
            </tr>
            @endforeach
        </table>
        @if($orders->lastPage() > 1)
        <div style="display:flex;align-items:center;justify-content:center;gap:4px;margin-top:20px;flex-wrap:wrap">
            <a href="{{ $orders->previousPageUrl() ?? '#' }}" style="display:inline-flex;align-items:center;justify-content:center;min-width:36px;height:36px;padding:0 10px;border:1px solid #ddd;border-radius:6px;text-decoration:none;color:{{ $orders->onFirstPage() ? '#ccc' : '#333' }};background:#fff;{{ $orders->onFirstPage() ? 'pointer-events:none' : '' }}">&laquo;</a>
            @php $cur = $orders->currentPage(); $last = $orders->lastPage(); $s = max(1,$cur-3); $e = min($last,$cur+3); @endphp
            @if($s > 1)
                <a href="{{ $orders->url(1) }}" style="display:inline-flex;align-items:center;justify-content:center;min-width:36px;height:36px;border:1px solid #ddd;border-radius:6px;text-decoration:none;color:#333;background:#fff">1</a>
                @if($s > 2) <span style="padding:0 5px;color:#ccc">...</span> @endif
            @endif
            @for($i = $s; $i <= $e; $i++)
                <a href="{{ $orders->url($i) }}" style="display:inline-flex;align-items:center;justify-content:center;min-width:36px;height:36px;border:1px solid {{ $i==$cur ? '#D0171C' : '#ddd' }};border-radius:6px;text-decoration:none;color:{{ $i==$cur ? '#fff' : '#333' }};background:{{ $i==$cur ? '#D0171C' : '#fff' }}">{{ $i }}</a>
            @endfor
            @if($e < $last)
                @if($e < $last-1) <span style="padding:0 5px;color:#ccc">...</span> @endif
                <a href="{{ $orders->url($last) }}" style="display:inline-flex;align-items:center;justify-content:center;min-width:36px;height:36px;border:1px solid #ddd;border-radius:6px;text-decoration:none;color:#333;background:#fff">{{ $last }}</a>
            @endif
            <a href="{{ $orders->nextPageUrl() ?? '#' }}" style="display:inline-flex;align-items:center;justify-content:center;min-width:36px;height:36px;padding:0 10px;border:1px solid #ddd;border-radius:6px;text-decoration:none;color:{{ $orders->hasMorePages() ? '#333' : '#ccc' }};background:#fff;{{ $orders->hasMorePages() ? '' : 'pointer-events:none' }}">&raquo;</a>
        </div>
        <div style="text-align:center;font-size:13px;color:#888;margin-top:8px">Показано {{ $orders->firstItem() }}–{{ $orders->lastItem() }} из {{ $orders->total() }}</div>
        @endif
    </div>
</div>
@endsection
