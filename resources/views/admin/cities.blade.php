@extends('layouts.admin')
@section('title', 'Города и зоны')
@push('styles')
<style>
    .cz-wrap { max-width: 760px; }
    .cz-card { background:#fff; border:1px solid #eaecef; border-radius:14px; margin-bottom:18px; overflow:hidden; }
    .cz-card__h { padding:14px 18px; border-bottom:1px solid #f0f1f4; font-weight:700; font-size:15px; }
    .cz-card__b { padding:16px 18px; }
    .cz-hint { color:#8a9099; font-size:12.5px; margin:4px 0 0; }

    .cz-add { display:flex; gap:8px; }
    .cz-add input, .cz-add select, .cz-input, .cz-select {
        border:1px solid #d7dbe0; border-radius:9px; padding:9px 12px; font-size:14px; outline:none; background:#fff; }
    .cz-add input:focus, .cz-select:focus, .cz-input:focus { border-color:#d0171c; }
    .cz-btn { border:none; border-radius:9px; padding:9px 16px; font-size:14px; font-weight:600; cursor:pointer; }
    .cz-btn--primary { background:#d0171c; color:#fff; } .cz-btn--primary:hover { background:#a81216; }
    .cz-btn--ghost { background:#f4f6f8; color:#444; } .cz-btn--ghost:hover { background:#e9edf1; }

    .cz-zones { display:flex; flex-wrap:wrap; gap:8px; margin-top:14px; }
    .cz-zone { display:inline-flex; align-items:center; gap:8px; background:#eef2f7; color:#2b3444; border-radius:999px; padding:6px 6px 6px 14px; font-size:13px; font-weight:600; }
    .cz-zone a { display:inline-flex; width:20px; height:20px; border-radius:50%; background:#cdd4de; color:#fff; align-items:center; justify-content:center; text-decoration:none; font-size:13px; line-height:1; }
    .cz-zone a:hover { background:#d0171c; }
    .cz-empty { color:#8a9099; font-size:13px; }

    table.cz-tbl { width:100%; border-collapse:collapse; }
    .cz-tbl th { text-align:left; font-size:11.5px; font-weight:700; letter-spacing:.04em; text-transform:uppercase; color:#9aa0a6; padding:0 10px 10px; }
    .cz-tbl td { padding:8px 10px; border-top:1px solid #f2f3f6; vertical-align:middle; }
    .cz-tbl tr:hover td { background:#fafbfc; }
    .cz-tbl .cz-city { font-weight:600; font-size:14px; }
    .cz-tbl .cz-select { width:100%; max-width:280px; }
    .cz-row-actions { display:flex; gap:8px; justify-content:flex-end; align-items:center; }
    .cz-del { color:#c2c6cd; text-decoration:none; font-size:18px; padding:4px 8px; border-radius:8px; }
    .cz-del:hover { color:#d0171c; background:#fdecec; }
    .cz-zoneform { display:flex; gap:8px; align-items:center; }
</style>
@endpush
@section('content')
<div class="cz-wrap">

    <div class="cz-card">
        <div class="cz-card__h">Зоны доставки</div>
        <div class="cz-card__b">
            <form action="{{ url('admin/zones/store') }}" method="post" class="cz-add">
                @csrf
                <input type="text" name="name" placeholder="Название зоны, напр. Алматы и область" style="flex:1" required>
                <button type="submit" class="cz-btn cz-btn--primary">Добавить зону</button>
            </form>
            <p class="cz-hint">Города одной зоны везём без склада. Сначала заведите зону здесь, потом выберите её у городов ниже.</p>
            <div class="cz-zones">
                @forelse($zones as $z)
                    <span class="cz-zone">{{ $z->name }}
                        <a href="/admin/zones/delete/{{ $z->id }}" title="Удалить зону"
                           onclick="return confirm('Удалить зону «{{ $z->name }}»? У городов этой зоны она снимется.')">×</a>
                    </span>
                @empty
                    <span class="cz-empty">Зон пока нет — добавьте первую выше.</span>
                @endforelse
            </div>
        </div>
    </div>

    <div class="cz-card">
        <div class="cz-card__h">Города</div>
        <div class="cz-card__b">
            <form action="{{ url('admin/cities/store') }}" method="post" class="cz-add" style="margin-bottom:16px">
                @csrf
                <input type="text" name="title" placeholder="Новый город" style="flex:1" required>
                <select name="zone" class="cz-select">
                    <option value="">— без зоны —</option>
                    @foreach($zones as $z)<option value="{{ $z->name }}">{{ $z->name }}</option>@endforeach
                </select>
                <button type="submit" class="cz-btn cz-btn--primary">Добавить</button>
            </form>

            <table class="cz-tbl">
                <thead><tr><th>Город</th><th>Зона доставки</th><th></th></tr></thead>
                <tbody>
                @foreach($cities as $c)
                <tr>
                    <td class="cz-city">{{ $c->title }}</td>
                    <td colspan="2">
                        <div class="cz-zoneform">
                            <form action="/admin/cities/{{ $c->id }}/update" method="post" style="display:flex;gap:8px;flex:1;margin:0">
                                @csrf
                                <input type="hidden" name="title" value="{{ $c->title }}">
                                <select name="zone" class="cz-select">
                                    <option value="">— без зоны —</option>
                                    @foreach($zones as $z)<option value="{{ $z->name }}" @selected($c->zone === $z->name)>{{ $z->name }}</option>@endforeach
                                    @if($c->zone && !$zones->contains('name', $c->zone))
                                        <option value="{{ $c->zone }}" selected>{{ $c->zone }} (нет в списке)</option>
                                    @endif
                                </select>
                                <button type="submit" class="cz-btn cz-btn--ghost">Сохранить</button>
                            </form>
                            <a href="/admin/cities/delete/{{ $c->id }}" class="cz-del" title="Удалить город"
                               onclick="return confirm('Удалить город «{{ $c->title }}»?')">🗑</a>
                        </div>
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
