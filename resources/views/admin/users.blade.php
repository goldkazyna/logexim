@extends('layouts.admin')
@section('title', 'Пользователи')
@section('content')
<div class="card">
    <div class="card-header">Все пользователи</div>
    <div class="card-body">
        <table>
            <tr><th>ID</th><th>БИН</th><th>Компания</th><th>Директор</th><th>Телефон</th><th>Email</th><th>Статус</th><th>Действие</th></tr>
            @foreach($users as $u)
            <tr>
                <td>{{ $u->id }}</td>
                <td>{{ $u->bin }}</td>
                <td>{{ $u->company_name }}</td>
                <td>{{ $u->director_name }}</td>
                <td>{{ $u->phone }}</td>
                <td>{{ $u->email }}</td>
                <td>{!! $u->activate ? '<span class="badge" style="background:#28a745">Активен</span>' : '<span class="badge" style="background:#dc3545">Не активен</span>' !!}</td>
                <td>
                    @if($u->activate)
                    <a href="/admin/users/deactivate/{{ $u->id }}" class="btn btn-danger btn-sm">Деактивировать</a>
                    @else
                    <a href="/admin/users/activate/{{ $u->id }}" class="btn btn-success btn-sm">Активировать</a>
                    @endif
                </td>
            </tr>
            @endforeach
        </table>
        @if($users->lastPage() > 1)
        <div style="display:flex;align-items:center;justify-content:center;gap:4px;margin-top:20px;flex-wrap:wrap">
            <a href="{{ $users->previousPageUrl() ?? '#' }}" style="display:inline-flex;align-items:center;justify-content:center;min-width:36px;height:36px;padding:0 10px;border:1px solid #ddd;border-radius:6px;text-decoration:none;color:{{ $users->onFirstPage() ? '#ccc' : '#333' }};background:#fff;{{ $users->onFirstPage() ? 'pointer-events:none' : '' }}">&laquo;</a>
            @php $cur = $users->currentPage(); $last = $users->lastPage(); $s = max(1,$cur-3); $e = min($last,$cur+3); @endphp
            @if($s > 1)
                <a href="{{ $users->url(1) }}" style="display:inline-flex;align-items:center;justify-content:center;min-width:36px;height:36px;border:1px solid #ddd;border-radius:6px;text-decoration:none;color:#333;background:#fff">1</a>
                @if($s > 2) <span style="padding:0 5px;color:#ccc">...</span> @endif
            @endif
            @for($i = $s; $i <= $e; $i++)
                <a href="{{ $users->url($i) }}" style="display:inline-flex;align-items:center;justify-content:center;min-width:36px;height:36px;border:1px solid {{ $i==$cur ? '#D0171C' : '#ddd' }};border-radius:6px;text-decoration:none;color:{{ $i==$cur ? '#fff' : '#333' }};background:{{ $i==$cur ? '#D0171C' : '#fff' }}">{{ $i }}</a>
            @endfor
            @if($e < $last)
                @if($e < $last-1) <span style="padding:0 5px;color:#ccc">...</span> @endif
                <a href="{{ $users->url($last) }}" style="display:inline-flex;align-items:center;justify-content:center;min-width:36px;height:36px;border:1px solid #ddd;border-radius:6px;text-decoration:none;color:#333;background:#fff">{{ $last }}</a>
            @endif
            <a href="{{ $users->nextPageUrl() ?? '#' }}" style="display:inline-flex;align-items:center;justify-content:center;min-width:36px;height:36px;padding:0 10px;border:1px solid #ddd;border-radius:6px;text-decoration:none;color:{{ $users->hasMorePages() ? '#333' : '#ccc' }};background:#fff;{{ $users->hasMorePages() ? '' : 'pointer-events:none' }}">&raquo;</a>
        </div>
        <div style="text-align:center;font-size:13px;color:#888;margin-top:8px">Показано {{ $users->firstItem() }}–{{ $users->lastItem() }} из {{ $users->total() }}</div>
        @endif
    </div>
</div>
@endsection
