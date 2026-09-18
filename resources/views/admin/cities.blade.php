@extends('layouts.admin')
@section('title', 'Города и направления')
@push('styles')
<style>
    .cz-wrap { max-width: 820px; }
    .cz-card { background:#fff; border:1px solid #eaecef; border-radius:14px; margin-bottom:18px; overflow:hidden; }
    .cz-card__h { padding:14px 18px; border-bottom:1px solid #f0f1f4; font-weight:700; font-size:15px; }
    .cz-card__b { padding:16px 18px; }
    .cz-hint { color:#8a9099; font-size:13px; margin:0 0 14px; line-height:1.45; }
    .cz-row { display:flex; gap:8px; align-items:center; flex-wrap:wrap; }
    .cz-input, .cz-select { border:1px solid #d7dbe0; border-radius:9px; padding:9px 12px; font-size:14px; outline:none; background:#fff; }
    .cz-input:focus, .cz-select:focus { border-color:#d0171c; }
    .cz-btn { border:none; border-radius:9px; padding:9px 16px; font-size:14px; font-weight:600; cursor:pointer; text-decoration:none; display:inline-block; }
    .cz-btn--primary { background:#d0171c; color:#fff; } .cz-btn--primary:hover { background:#a81216; }
    .cz-btn--ghost { background:#f4f6f8; color:#444; } .cz-btn--ghost:hover { background:#e9edf1; }

    .cz-checks { display:grid; grid-template-columns:repeat(auto-fill,minmax(180px,1fr)); gap:8px 16px; margin:14px 0 16px; }
    .cz-checks label { display:flex; align-items:center; gap:8px; font-size:14px; cursor:pointer; }

    .cz-summary { margin-top:4px; }
    .cz-summary__item { padding:8px 0; border-top:1px solid #f2f3f6; font-size:14px; }
    .cz-summary__item b { font-weight:700; }
    .cz-summary__to { color:#2b6cb0; }
    .cz-empty { color:#8a9099; font-size:13px; }

    table.cz-tbl { width:100%; border-collapse:collapse; }
    .cz-tbl td { padding:9px 10px; border-top:1px solid #f2f3f6; font-size:14px; }
    .cz-tbl tr:hover td { background:#fafbfc; }
    .cz-del { color:#c2c6cd; text-decoration:none; font-size:17px; }
    .cz-del:hover { color:#d0171c; }
</style>
@endpush
@section('content')
<div class="cz-wrap">

    <div class="cz-card">
        <div class="cz-card__h">Доставка без склада — прямые направления</div>
        <div class="cz-card__b">
            <p class="cz-hint">Выберите город и отметьте, <b>в какие города из него доставка идёт напрямую, без склада</b>. Связь работает в обе стороны (Алматы ↔ Шымкент). Можно отметить сколько угодно городов.</p>

            <form method="get" action="/admin/cities" class="cz-row">
                <span>Город:</span>
                <select name="city" class="cz-select" onchange="this.form.submit()">
                    <option value="">— выберите город —</option>
                    @foreach($cities as $c)
                        <option value="{{ $c->id }}" @selected($selectedCity && $selectedCity->id === $c->id)>{{ $c->title }}</option>
                    @endforeach
                </select>
                <noscript><button class="cz-btn cz-btn--ghost">Открыть</button></noscript>
            </form>

            @if($selectedCity)
                <form action="/admin/cities/{{ $selectedCity->id }}/links" method="post">
                    @csrf
                    <div class="cz-checks">
                        @foreach($cities as $c)
                            @if($c->id !== $selectedCity->id)
                                <label>
                                    <input type="checkbox" name="linked[]" value="{{ $c->id }}" @checked(in_array($c->id, $linkedIds))>
                                    {{ $c->title }}
                                </label>
                            @endif
                        @endforeach
                    </div>
                    <button type="submit" class="cz-btn cz-btn--primary">Сохранить направления для «{{ $selectedCity->title }}»</button>
                </form>
            @endif

            <div class="cz-summary">
                <p class="cz-hint" style="margin:18px 0 6px"><b>Настроенные направления:</b></p>
                @php $has = false; @endphp
                @foreach($cities as $c)
                    @if(!empty($linksByCity[$c->id]))
                        @php $has = true; @endphp
                        <div class="cz-summary__item"><b>{{ $c->title }}</b> → <span class="cz-summary__to">{{ implode(', ', array_filter($linksByCity[$c->id])) }}</span></div>
                    @endif
                @endforeach
                @unless($has)<span class="cz-empty">Пока ничего не настроено.</span>@endunless
            </div>
        </div>
    </div>

    <div class="cz-card">
        <div class="cz-card__h">Города</div>
        <div class="cz-card__b">
            <form action="{{ url('admin/cities/store') }}" method="post" class="cz-row" style="margin-bottom:14px">
                @csrf
                <input type="text" name="title" class="cz-input" placeholder="Новый город" style="flex:1" required>
                <button type="submit" class="cz-btn cz-btn--primary">Добавить</button>
            </form>
            <table class="cz-tbl">
                @foreach($cities as $c)
                <tr>
                    <td>{{ $c->title }}</td>
                    <td style="text-align:right">
                        <a href="/admin/cities?city={{ $c->id }}" class="cz-btn cz-btn--ghost" style="padding:5px 12px;font-size:13px">Направления</a>
                        <a href="/admin/cities/delete/{{ $c->id }}" class="cz-del" title="Удалить город" onclick="return confirm('Удалить город «{{ $c->title }}»?')" style="margin-left:8px">🗑</a>
                    </td>
                </tr>
                @endforeach
            </table>
        </div>
    </div>

</div>
@endsection
