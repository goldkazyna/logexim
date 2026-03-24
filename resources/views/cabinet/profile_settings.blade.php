@extends('layouts.cabinet')
@section('title', 'Настройка профиля')
@push('styles')
<style>.common_btn{background-color:#D0171C;border-color:#D0171C;border-radius:10px;color:#fff;padding:8px 20px;border:none;cursor:pointer}.common_btn:hover{background-color:#a21216}</style>
@endpush
@section('content')
<main class="flex-grow p-6">
    <div class="flex items-center justify-between flex-wrap gap-2 mb-6">
        <div>
            <h4 class="text-slate-900 text-lg font-medium mb-2">Настройка профиля</h4>
            <div class="md:flex hidden items-center gap-3 text-sm font-semibold">
                <p>Пожалуйста, обновите свои данные, если это необходимо.<br>
                <span style="color:red;font-weight:bold">БИН изменять нельзя!</span><br>
                <span style="color:red;font-weight:bold">Указывайте корректный Email для восстановления пароля.</span></p>
            </div>
            @if(session('success'))
            <div id="success-alert" class="bg-primary text-sm text-white rounded-md p-4 mb-4 mt-4">{{ session('success') }}</div>
            @endif
            <form action="{{ url('cabinet/update_profile') }}" method="post" class="mt-4">
                @csrf
                <div class="mb-4">
                    <label class="text-gray-800 font-bold text-base inline-block mb-2">Имя</label>
                    <input type="text" name="director_name" class="form-input w-full md:w-1/2" value="{{ $user->director_name }}" required>
                </div>
                <div class="mb-4">
                    <label class="text-gray-800 font-bold text-base inline-block mb-2">Email</label>
                    <input type="email" name="email" class="form-input w-full md:w-1/2" value="{{ $user->email }}" required>
                </div>
                <div class="mb-4">
                    <label class="text-gray-800 font-bold text-base inline-block mb-2">Телефон</label>
                    <input type="text" name="phone" class="form-input w-full md:w-1/2" value="{{ $user->phone }}" required>
                </div>
                <div class="mb-4">
                    <label class="text-gray-800 font-bold text-base inline-block mb-2">Компания</label>
                    <input type="text" name="company_name" class="form-input w-full md:w-1/2" value="{{ $user->company_name }}">
                </div>
                <div class="mb-4">
                    <label class="text-gray-800 font-bold text-base inline-block mb-2">Город</label>
                    <input type="text" name="city" class="form-input w-full md:w-1/2" value="{{ $user->city }}">
                </div>
                <div class="mb-4">
                    <label class="text-gray-800 font-bold text-base inline-block mb-2">Страна</label>
                    <input type="text" name="country" class="form-input w-full md:w-1/2" value="{{ $user->country }}">
                </div>
                <div class="mb-4">
                    <label class="text-gray-800 font-bold text-base inline-block mb-2">Область</label>
                    <input type="text" name="region" class="form-input w-full md:w-1/2" value="{{ $user->region }}">
                </div>
                <div class="mb-4">
                    <label class="text-gray-800 font-bold text-base inline-block mb-2">Район</label>
                    <input type="text" name="district" class="form-input w-full md:w-1/2" value="{{ $user->district }}">
                </div>
                <div class="mb-4">
                    <label class="text-gray-800 font-bold text-base inline-block mb-2">Адрес</label>
                    <textarea name="address" class="form-input w-full md:w-1/2" rows="3">{{ $user->address }}</textarea>
                </div>
                <button type="submit" class="common_btn">Сохранить изменения</button>
            </form>
        </div>
    </div>
</main>
@endsection
@push('scripts')
<script>$(document).ready(function(){setTimeout(function(){$('#success-alert').fadeOut('slow')},5000)});</script>
@endpush
