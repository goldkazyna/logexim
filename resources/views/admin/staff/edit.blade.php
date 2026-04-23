@extends('layouts.admin')

@section('title', 'Редактирование сотрудника')

@push('scripts')
<script>
function toggleWarehouseField() {
    var role = document.getElementById('role-select').value;
    document.getElementById('warehouse-location-row').style.display = role === 'warehouse' ? '' : 'none';
}
</script>
@endpush

@section('content')
<div class="card">
    <div class="card-header">Редактирование сотрудника</div>
    <div class="card-body">
        @if($errors->any())
        <div class="alert" style="background:#f8d7da;color:#721c24">
            @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
        </div>
        @endif
        <form action="/admin/staff/{{ $item->id }}" method="POST">
            @csrf
            <div class="form-group">
                <label>ФИО *</label>
                <input type="text" name="full_name" class="form-control" value="{{ old('full_name', $item->full_name) }}" required>
            </div>
            <div class="form-group">
                <label>Логин *</label>
                <input type="text" name="login" class="form-control" value="{{ old('login', $item->login) }}" required>
            </div>
            <div class="form-group">
                <label>Новый пароль <small style="color:#888">(оставьте пустым, чтобы не менять)</small></label>
                <input type="text" name="password" class="form-control" value="">
            </div>
            <div class="form-group">
                <label>Роль *</label>
                <select name="role" id="role-select" class="form-control" required onchange="toggleWarehouseField()">
                    <option value="dispatcher" {{ old('role', $item->role) === 'dispatcher' ? 'selected' : '' }}>Диспетчер</option>
                    <option value="courier" {{ old('role', $item->role) === 'courier' ? 'selected' : '' }}>Курьер</option>
                    <option value="warehouse" {{ old('role', $item->role) === 'warehouse' ? 'selected' : '' }}>Кладовщик</option>
                </select>
            </div>
            <div class="form-group" id="warehouse-location-row" style="{{ old('role', $item->role) === 'warehouse' ? '' : 'display:none' }}">
                <label>Локация склада</label>
                <input type="text" name="warehouse_location" class="form-control" value="{{ old('warehouse_location', $item->warehouse_location) }}" placeholder="Например: Алматы — Центральный">
            </div>
            <div class="form-group">
                <label>Телефон</label>
                <input type="text" name="phone" class="form-control" value="{{ old('phone', $item->phone) }}">
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email', $item->email) }}">
            </div>
            <div class="form-group">
                <label>Заметка</label>
                <textarea name="note" class="form-control" rows="3">{{ old('note', $item->note) }}</textarea>
            </div>
            <div class="form-group">
                <label><input type="checkbox" name="active" value="1" {{ $item->active ? 'checked' : '' }}> Активен</label>
            </div>
            <button type="submit" class="btn btn-primary">Сохранить</button>
            <a href="/admin/staff" class="btn" style="background:#6c757d;color:#fff">Отмена</a>
        </form>
    </div>
</div>
@endsection
