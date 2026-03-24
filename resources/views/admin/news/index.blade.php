@extends('layouts.admin')
@section('title', 'Новости')
@section('content')
<a href="/admin/news/create" class="btn btn-primary" style="margin-bottom:15px">Добавить новость</a>
<div class="card">
    <div class="card-header">Все новости</div>
    <div class="card-body">
        <table>
            <tr><th>ID</th><th>Заголовок</th><th>Дата</th><th>Действие</th></tr>
            @foreach($news as $n)
            <tr>
                <td>{{ $n->id }}</td>
                <td>{{ $n->title }}</td>
                <td>{{ $n->date }}</td>
                <td>
                    <a href="/admin/news/edit/{{ $n->id }}" class="btn btn-sm btn-primary">Ред.</a>
                    <a href="/admin/news/delete/{{ $n->id }}" class="btn btn-sm btn-danger" onclick="return confirm('Удалить?')">Уд.</a>
                </td>
            </tr>
            @endforeach
        </table>
    </div>
</div>
@endsection
