@extends('layouts.admin')

@section('title', 'Сотрудники')

@push('styles')
<style>
    .stf-head { display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap; }
    .stf-groups { display:flex; flex-direction:column; gap:26px; }
    .stf-group__h { display:flex; align-items:center; gap:10px; font-size:13px; font-weight:700;
        letter-spacing:.06em; text-transform:uppercase; color:#6b7280; margin:0 0 10px; }
    .stf-count { background:#f1f3f6; color:#4b5563; border-radius:999px; padding:1px 10px; font-size:12px; font-weight:700; }
    .stf-row { display:flex; align-items:center; gap:14px; padding:11px 12px; border-radius:12px; transition:background .15s; }
    .stf-row:hover { background:#f7f8fa; }
    .stf-row + .stf-row { border-top:1px solid #f0f1f4; }
    .stf-av { flex:none; width:38px; height:38px; border-radius:50%; display:flex; align-items:center;
        justify-content:center; color:#fff; font-size:13px; font-weight:700; }
    .stf-id { flex:1; min-width:0; }
    .stf-name { font-weight:700; color:#1a1a1a; font-size:14.5px; }
    .stf-login { color:#9aa0a6; font-size:12.5px; margin-top:1px; }
    .stf-cities { color:#374151; font-size:12px; margin-top:3px; display:flex; align-items:center; gap:5px; flex-wrap:wrap; }
    .stf-cities svg{ width:13px; height:13px; color:#9aa0a6; flex:none; }
    .stf-city { background:#eef2f7; color:#334155; border-radius:6px; padding:1px 7px; font-size:11.5px; white-space:nowrap; }
    .stf-chips { display:flex; flex-wrap:wrap; gap:5px; width:230px; }
    .stf-chip { font-size:11.5px; font-weight:600; padding:3px 9px; border-radius:999px; white-space:nowrap; }
    .stf-chip.courier    { color:#2563eb; background:#e8f0fe; }
    .stf-chip.agent      { color:#7c3aed; background:#f1ecfe; }
    .stf-chip.warehouse  { color:#ea580c; background:#fdeede; }
    .stf-chip.dispatcher { color:#0f766e; background:#e2f5f2; }
    .stf-phone { width:120px; font-size:13.5px; color:#374151; font-variant-numeric:tabular-nums; }
    .stf-status { width:110px; display:flex; align-items:center; gap:7px; font-size:13px; font-weight:600; }
    .stf-dot { width:8px; height:8px; border-radius:50%; flex:none; }
    .stf-status.on  { color:#16a34a; } .stf-status.on  .stf-dot { background:#16a34a; }
    .stf-status.off { color:#9aa0a6; } .stf-status.off .stf-dot { background:#c2c6cd; }
    .stf-actions { display:flex; gap:6px; }
    .stf-ic { width:34px; height:34px; padding:0; border-radius:9px; border:1px solid #e6e8ee; background:#fff;
        color:#6b7280; display:flex; align-items:center; justify-content:center; cursor:pointer; transition:.15s; }
    .stf-ic:hover { color:#1a1a1a; border-color:#b9bec7; }
    .stf-ic.danger:hover { color:#d0171c; border-color:#d0171c; }
    .stf-ic.on:hover { color:#16a34a; border-color:#16a34a; }
    .stf-ic svg { width:16px; height:16px; }
    .stf-form { display:inline; margin:0; }
    @media (max-width:900px){
        .stf-chips{ width:auto; } .stf-phone{ width:auto; } .stf-status{ width:auto; }
        .stf-row{ flex-wrap:wrap; }
    }
</style>
@endpush

@section('content')
@php
    $order = [
        'courier'    => 'Курьеры',
        'agent'      => 'Агенты',
        'warehouse'  => 'Кладовщики',
        'dispatcher' => 'Диспетчеры',
    ];
    $chipLabels = \App\Models\Staff::ROLE_LABELS;
    $avColors = ['#d0171c','#2563eb','#7c3aed','#ea580c','#0f766e','#0891b2','#c026d3','#65a30d'];
    $avatar = function ($name) use ($avColors) {
        $parts = preg_split('/\s+/', trim((string) $name));
        $ini = mb_strtoupper(mb_substr($parts[0] ?? '', 0, 1) . mb_substr($parts[1] ?? '', 0, 1));
        $sum = array_sum(array_map('ord', str_split((string) $name)));
        return ['ini' => $ini, 'color' => $avColors[$sum % count($avColors)]];
    };
@endphp

<div class="card">
    <div class="card-header stf-head">
        <span>Сотрудники</span>
        <a href="/admin/staff/create" class="btn btn-primary btn-sm">+ Добавить сотрудника</a>
    </div>
    <div class="card-body">
        @if(count($staff) === 0)
            <p style="color:#888">Сотрудников пока нет.</p>
        @else
        <div class="stf-groups">
            @foreach($order as $roleKey => $groupTitle)
                @php $list = $staff->filter(fn ($s) => in_array($roleKey, $s->roleNames(), true))->values(); @endphp
                @if($list->count())
                <div class="stf-group">
                    <div class="stf-group__h">{{ $groupTitle }}<span class="stf-count">{{ $list->count() }}</span></div>
                    @foreach($list as $s)
                        @php $a = $avatar($s->full_name); @endphp
                        <div class="stf-row">
                            <span class="stf-av" style="background:{{ $a['color'] }}">{{ $a['ini'] }}</span>
                            <div class="stf-id">
                                <div class="stf-name">{{ $s->full_name }}</div>
                                <div class="stf-login">{{ $s->login }}@if($s->email) · {{ $s->email }}@endif</div>
                                @if($s->cities->count())
                                <div class="stf-cities">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                                    @foreach($s->cities as $c)<span class="stf-city">{{ $c->title }}</span>@endforeach
                                </div>
                                @endif
                            </div>
                            <div class="stf-chips">
                                @foreach($s->roleNames() as $r)
                                    <span class="stf-chip {{ $r }}">{{ $chipLabels[$r] ?? $r }}</span>
                                @endforeach
                            </div>
                            <div class="stf-phone">{{ $s->phone }}</div>
                            <div class="stf-status {{ $s->active ? 'on' : 'off' }}">
                                <span class="stf-dot"></span>{{ $s->active ? 'Активен' : 'Отключён' }}
                            </div>
                            <div class="stf-actions">
                                <a href="/admin/staff/{{ $s->id }}/edit" class="stf-ic" title="Редактировать">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                                </a>
                                <form action="/admin/staff/{{ $s->id }}/toggle" method="POST" class="stf-form">
                                    @csrf
                                    <button type="submit" class="stf-ic {{ $s->active ? '' : 'on' }}" title="{{ $s->active ? 'Отключить' : 'Включить' }}">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v10"/><path d="M18.4 6.6a9 9 0 1 1-12.8 0"/></svg>
                                    </button>
                                </form>
                                <form action="/admin/staff/{{ $s->id }}/delete" method="POST" class="stf-form" onsubmit="return confirm('Удалить сотрудника?')">
                                    @csrf
                                    <button type="submit" class="stf-ic danger" title="Удалить">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M8 6V4h8v2M19 6l-1 14H6L5 6"/></svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
                @endif
            @endforeach
        </div>
        @endif
    </div>
</div>
@endsection
