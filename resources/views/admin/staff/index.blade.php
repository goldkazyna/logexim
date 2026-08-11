@extends('layouts.admin')

@section('title', 'Сотрудники')

@section('content')
<div class="card">
    <div class="card-header" style="display:flex;justify-content:space-between;align-items:center">
        <span>Сотрудники (курьеры и диспетчера)</span>
        <a href="/admin/staff/create" class="btn btn-primary btn-sm">+ Добавить сотрудника</a>
    </div>
    <div class="card-body">
        @if(count($staff) === 0)
            <p style="color:#888">Сотрудников пока нет.</p>
        @else
        <table>
            <thead>
                <tr>
                    <th>ФИО</th>
                    <th>Логин</th>
                    <th>Роль</th>
                    <th>Телефон</th>
                    <th>Email</th>
                    <th>Статус</th>
                    <th style="width:280px">Действия</th>
                </tr>
            </thead>
            <tbody>
                @foreach($staff as $s)
                <tr>
                    <td>{{ $s->full_name }}</td>
                    <td>{{ $s->login }}</td>
                    <td>
                        {{ $s->roleLabel() }}
                    </td>
                    <td>{{ $s->phone }}</td>
                    <td>{{ $s->email }}</td>
                    <td>
                        @if($s->active)
                            <span class="badge" style="background:#28a745">Активен</span>
                        @else
                            <span class="badge" style="background:#6c757d">Отключен</span>
                        @endif
                    </td>
                    <td>
                        <a href="/admin/staff/{{ $s->id }}/edit" class="btn btn-sm btn-primary">Редактировать</a>
                        <form action="/admin/staff/{{ $s->id }}/toggle" method="POST" style="display:inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-success">{{ $s->active ? 'Выкл' : 'Вкл' }}</button>
                        </form>
                        <form action="/admin/staff/{{ $s->id }}/delete" method="POST" style="display:inline" onsubmit="return confirm('Удалить сотрудника?')">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-danger">Удалить</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>
</div>
@endsection
