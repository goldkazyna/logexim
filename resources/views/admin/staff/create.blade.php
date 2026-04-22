@extends('layouts.admin')

@section('title', 'Новый сотрудник')

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
                <label>Роль *</label>
                <select name="role" class="form-control" required>
                    <option value="dispatcher" {{ old('role') === 'dispatcher' ? 'selected' : '' }}>Диспетчер</option>
                    <option value="courier" {{ old('role') === 'courier' ? 'selected' : '' }}>Курьер</option>
                </select>
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
