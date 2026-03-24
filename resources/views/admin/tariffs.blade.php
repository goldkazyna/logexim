@extends('layouts.admin')
@section('title', 'Тарифы ' . $typeName)
@section('content')
<div class="card" style="margin-bottom:20px">
    <div class="card-header">Добавить тариф</div>
    <div class="card-body">
        <form action="{{ url('admin/tariffs/'.$type.'/store') }}" method="post" style="display:flex;gap:10px;align-items:end;flex-wrap:wrap">
            @csrf
            <div class="form-group" style="margin:0"><label>Откуда</label>
                <select name="city_from" class="form-control" required>
                    <option value="">--</option>
                    @foreach($cities as $c)<option value="{{ $c->id }}">{{ $c->title }}</option>@endforeach
                </select>
            </div>
            <div class="form-group" style="margin:0"><label>Куда</label>
                <select name="city_to" class="form-control" required>
                    <option value="">--</option>
                    @foreach($cities as $c)<option value="{{ $c->id }}">{{ $c->title }}</option>@endforeach
                </select>
            </div>
            <div class="form-group" style="margin:0"><label>Цена</label><input type="number" name="price" class="form-control" required></div>
            <div class="form-group" style="margin:0"><label>Время</label><input type="text" name="time" class="form-control" required></div>
            <button type="submit" class="btn btn-primary">Добавить</button>
        </form>
    </div>
</div>
<div class="card">
    <div class="card-header">Все тарифы</div>
    <div class="card-body">
        <table>
            <tr><th>ID</th><th>Откуда</th><th>Куда</th><th>Цена</th><th>Время</th><th>Действие</th></tr>
            @foreach($tariffs as $t)
            <tr>
                <td>{{ $t->id }}</td>
                <td>{{ $t->cityFrom->title ?? $t->city_from }}</td>
                <td>{{ $t->cityTo->title ?? $t->city_to }}</td>
                <td>{{ $t->price }}</td>
                <td>{{ $t->time }}</td>
                <td><a href="/admin/tariffs/{{ $type }}/delete/{{ $t->id }}" class="btn btn-sm btn-danger" onclick="return confirm('Удалить?')">Удалить</a></td>
            </tr>
            @endforeach
        </table>
    </div>
</div>
@endsection
