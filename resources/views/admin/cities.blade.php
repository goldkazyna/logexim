@extends('layouts.admin')
@section('title', 'Города доставки')
@section('content')
<div class="card" style="margin-bottom:20px">
    <div class="card-header">Добавить город</div>
    <div class="card-body">
        <form action="{{ url('admin/cities/store') }}" method="post" style="display:flex;gap:10px;align-items:end;flex-wrap:wrap">
            @csrf
            <div class="form-group" style="flex:1;min-width:180px;margin:0"><label>Название</label><input type="text" name="title" class="form-control" required></div>
            <div class="form-group" style="flex:1;min-width:180px;margin:0"><label>Зона доставки</label><input type="text" name="zone" class="form-control" placeholder="напр. Алматы" list="zone-list"></div>
            <button type="submit" class="btn btn-primary">Добавить</button>
        </form>
        <small style="color:#888;display:block;margin-top:8px">Города с одинаковой зоной считаются одной областью — доставка между ними идёт без склада (например, зона «Алматы» для Алматы и городов области).</small>
    </div>
</div>
<datalist id="zone-list">
    @foreach($cities->pluck('zone')->filter()->unique()->sort() as $z)<option value="{{ $z }}">@endforeach
</datalist>
<div class="card">
    <div class="card-header">Все города</div>
    <div class="card-body">
        <table>
            <tr><th>ID</th><th>Название</th><th>Зона</th><th>Действие</th></tr>
            @foreach($cities as $c)
            <tr>
                <td>{{ $c->id }}</td>
                <td>{{ $c->title }}</td>
                <td>
                    <form action="/admin/cities/{{ $c->id }}/update" method="post" style="display:flex;gap:6px;align-items:center;margin:0">
                        @csrf
                        <input type="hidden" name="title" value="{{ $c->title }}">
                        <input type="text" name="zone" value="{{ $c->zone }}" class="form-control" style="width:180px;padding:4px 8px" placeholder="без зоны" list="zone-list">
                        <button type="submit" class="btn btn-sm btn-primary">Сохранить</button>
                    </form>
                </td>
                <td><a href="/admin/cities/delete/{{ $c->id }}" class="btn btn-sm btn-danger" onclick="return confirm('Удалить?')">Удалить</a></td>
            </tr>
            @endforeach
        </table>
    </div>
</div>
@endsection
