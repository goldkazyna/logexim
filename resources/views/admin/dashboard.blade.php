@extends('layouts.admin')
@section('title', 'Дашборд')
@section('content')
<div class="stats">
    <div class="stat-card"><h3>{{ $usersCount }}</h3><p>Пользователей</p></div>
    <div class="stat-card"><h3>{{ $invoicesCount }}</h3><p>Накладных</p></div>
    <div class="stat-card"><h3>{{ $ordersCount }}</h3><p>Заказов</p></div>
    <div class="stat-card"><h3>{{ $newsCount }}</h3><p>Новостей</p></div>
    <div class="stat-card"><h3>{{ $citiesCount }}</h3><p>Городов</p></div>
    <div class="stat-card"><h3>{{ $pagesCount }}</h3><p>Страниц</p></div>
</div>
<div class="card">
    <div class="card-header">Последние регистрации</div>
    <div class="card-body">
        <table>
            <tr><th>БИН</th><th>Компания</th><th>Телефон</th><th>Дата</th><th>Статус</th></tr>
            @foreach($latestUsers as $u)
            <tr>
                <td>{{ $u->bin }}</td>
                <td>{{ $u->company_name }}</td>
                <td>{{ $u->phone }}</td>
                <td>{{ $u->date }}</td>
                <td>{!! $u->activate ? '<span class="badge" style="background:#28a745">Активен</span>' : '<span class="badge" style="background:#dc3545">Не активен</span>' !!}</td>
            </tr>
            @endforeach
        </table>
    </div>
</div>
@endsection
