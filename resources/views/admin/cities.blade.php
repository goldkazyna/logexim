@extends('layouts.admin')
@section('title', 'Города доставки')
@section('content')

<div class="card" style="margin-bottom:20px">
    <div class="card-header">Зоны доставки</div>
    <div class="card-body">
        <form action="{{ url('admin/zones/store') }}" method="post" style="display:flex;gap:10px;align-items:end;flex-wrap:wrap;margin-bottom:14px">
            @csrf
            <div class="form-group" style="flex:1;min-width:200px;margin:0"><label>Новая зона</label><input type="text" name="name" class="form-control" placeholder="напр. Алматы и область" required></div>
            <button type="submit" class="btn btn-primary">Добавить зону</button>
        </form>
        <small style="color:#888;display:block;margin-bottom:10px">Зона — это район доставки. Городам одной зоны доставка идёт без склада. Заведите зоны здесь, затем выберите их у городов ниже.</small>
        @if($zones->isEmpty())
            <p style="color:#888;margin:0">Зон пока нет.</p>
        @else
        <div style="display:flex;flex-wrap:wrap;gap:8px">
            @foreach($zones as $z)
                <span style="display:inline-flex;align-items:center;gap:8px;background:#eef2f7;border-radius:999px;padding:5px 6px 5px 14px;font-size:13px">
                    {{ $z->name }}
                    <a href="/admin/zones/delete/{{ $z->id }}" onclick="return confirm('Удалить зону «{{ $z->name }}»? У городов этой зоны она снимется.')" style="display:inline-flex;width:20px;height:20px;border-radius:50%;background:#d0171c;color:#fff;align-items:center;justify-content:center;text-decoration:none;line-height:1">×</a>
                </span>
            @endforeach
        </div>
        @endif
    </div>
</div>

<div class="card" style="margin-bottom:20px">
    <div class="card-header">Добавить город</div>
    <div class="card-body">
        <form action="{{ url('admin/cities/store') }}" method="post" style="display:flex;gap:10px;align-items:end;flex-wrap:wrap">
            @csrf
            <div class="form-group" style="flex:1;min-width:180px;margin:0"><label>Название</label><input type="text" name="title" class="form-control" required></div>
            <div class="form-group" style="flex:1;min-width:180px;margin:0">
                <label>Зона доставки</label>
                <select name="zone" class="form-control">
                    <option value="">— без зоны —</option>
                    @foreach($zones as $z)<option value="{{ $z->name }}">{{ $z->name }}</option>@endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Добавить</button>
        </form>
    </div>
</div>

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
                        <select name="zone" class="form-control" style="width:200px;padding:4px 8px">
                            <option value="">— без зоны —</option>
                            @foreach($zones as $z)<option value="{{ $z->name }}" @selected($c->zone === $z->name)>{{ $z->name }}</option>@endforeach
                            @if($c->zone && !$zones->contains('name', $c->zone))
                                <option value="{{ $c->zone }}" selected>{{ $c->zone }} (нет в списке)</option>
                            @endif
                        </select>
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
