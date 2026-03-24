@extends('layouts.admin')
@section('title', 'Страницы')
@section('content')
<div class="card">
    <div class="card-header">Все страницы</div>
    <div class="card-body">
        <table>
            <tr><th>ID</th><th>Заголовок</th><th>Alias</th><th>Действие</th></tr>
            @foreach($pages as $p)
            <tr>
                <td>{{ $p->id }}</td>
                <td>{{ $p->title }}</td>
                <td>/{{ $p->alias }}</td>
                <td><a href="/admin/pages/edit/{{ $p->id }}" class="btn btn-sm btn-primary">Ред.</a></td>
            </tr>
            @endforeach
        </table>
    </div>
</div>
@endsection
