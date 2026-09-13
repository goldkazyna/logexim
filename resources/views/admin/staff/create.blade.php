@extends('layouts.admin')

@section('title', 'Новый сотрудник')

@push('scripts')
<script>
// Локация склада нужна, только если среди отмеченных ролей есть «Кладовщик».
function toggleWarehouseField() {
    var checkbox = document.querySelector('input[name="roles[]"][value="warehouse"]');
    document.getElementById('warehouse-location-row').style.display =
        checkbox && checkbox.checked ? '' : 'none';
}
</script>
<style>
    .role-checks { display: flex; flex-wrap: wrap; gap: 8px 22px; padding: 4px 0; }
    .role-checks label { display: inline-flex; align-items: center; gap: 6px; font-weight: normal; cursor: pointer; margin: 0; }
    .city-checks { display:grid; grid-template-columns:repeat(auto-fill,minmax(150px,1fr)); gap:6px 14px;
        max-height:210px; overflow:auto; padding:10px 12px; border:1px solid #e6e8ee; border-radius:8px; background:#fafbfc; }
    .city-checks label { display:inline-flex; align-items:center; gap:7px; font-weight:normal; cursor:pointer; margin:0; font-size:14px; }
</style>

@endpush

@section('content')
<div class="card">
    <div class="card-header">Новый сотрудник</div>
    <div class="card-body">
        @if($errors->any())
        <div class="alert" style="background:#f8d7da;color:#721c24">
            @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
        </div>
        @endif
        <form action="/admin/staff" method="POST">
            @csrf
            <div class="form-group">
                <label>ФИО *</label>
                <input type="text" name="full_name" class="form-control" value="{{ old('full_name') }}" required>
            </div>
            <div class="form-group">
                <label>Логин *</label>
                <input type="text" name="login" class="form-control" value="{{ old('login') }}" required>
            </div>
            <div class="form-group">
                <label>Пароль *</label>
                <input type="text" name="password" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Роли *</label>
                <div class="role-checks">
                    @foreach(\App\Models\Staff::ROLE_LABELS as $value => $label)
                        <label>
                            <input type="checkbox" name="roles[]" value="{{ $value }}"
                                   onchange="toggleWarehouseField()"
                                   {{ in_array($value, (array) old('roles', ['courier']), true) ? 'checked' : '' }}>
                            {{ $label }}
                        </label>
                    @endforeach
                </div>
                <small style="color:#888">Можно отметить несколько — сотрудник сам переключается между ними в приложении.</small>
            </div>
            <div class="form-group" id="warehouse-location-row" style="{{ in_array('warehouse', (array) old('roles', []), true) ? '' : 'display:none' }}">
                <label>Локация склада</label>
                <input type="text" name="warehouse_location" class="form-control" value="{{ old('warehouse_location') }}" placeholder="Например: Алматы — Центральный">
            </div>
            <div class="form-group">
                <label>Города обслуживания</label>
                <div class="city-checks">
                    @forelse($cities as $city)
                        <label>
                            <input type="checkbox" name="city_ids[]" value="{{ $city->id }}"
                                   {{ in_array($city->id, (array) old('city_ids', []), true) ? 'checked' : '' }}>
                            {{ $city->title }}
                        </label>
                    @empty
                        <span style="color:#888">Справочник городов пуст — добавьте города в разделе «Города».</span>
                    @endforelse
                </div>
                <small style="color:#888">Отметьте города, которые обслуживает сотрудник. Можно несколько.</small>
            </div>
            <div class="form-group">
                <label>Телефон</label>
                <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}">
            </div>
            <div class="form-group">
                <label>Заметка</label>
                <textarea name="note" class="form-control" rows="3">{{ old('note') }}</textarea>
            </div>
            <div class="form-group">
                <label><input type="checkbox" name="active" value="1" checked> Активен</label>
            </div>
            <button type="submit" class="btn btn-primary">Сохранить</button>
            <a href="/admin/staff" class="btn" style="background:#6c757d;color:#fff">Отмена</a>
        </form>
    </div>
</div>
@endsection
