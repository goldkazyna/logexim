@extends('layouts.admin')
@section('title', 'Города доставки')
@section('content')
<div class="card" style="margin-bottom:20px">
    <div class="card-header">Добавить город</div>
    <div class="card-body">
        <form action="{{ url('admin/cities/store') }}" method="post" style="display:flex;gap:10px;align-items:end">
            @csrf
            <div class="form-group" style="flex:1;margin:0"><label>Название</label><input type="text" name="title" class="form-control" required></div>
            <button type="submit" class="btn btn-primary">Добавить</button>
        </form>
    </div>
</div>
<div class="card">
    <div class="card-header">Все города</div>
    <div class="card-body">
        <table>
            <tr><th>ID</th><th>Название</th><th>Действие</th></tr>
            @foreach($cities as $c)
            <tr>
                <td>{{ $c->id }}</td>
                <td>{{ $c->title }}</td>
                <td><a href="/admin/cities/delete/{{ $c->id }}" class="btn btn-sm btn-danger" onclick="return confirm('Удалить?')">Удалить</a></td>
            </tr>
            @endforeach
        </table>
    </div>
</div>
@endsection
