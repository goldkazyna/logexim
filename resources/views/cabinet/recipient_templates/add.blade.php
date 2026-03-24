@extends('layouts.cabinet')
@section('title', 'Добавить отправителя')
@push('styles')
<style>.common_btn{background-color:#D0171C;border-color:#D0171C;border-radius:10px;color:#fff;padding:8px 20px;border:none;cursor:pointer}.common_btn:hover{background-color:#a21216}</style>
@endpush
@section('content')
<main class="flex-grow p-6">
    <h4 class="text-slate-900 text-lg font-medium mb-4">Добавить отправителя</h4>
    <form action="{{ url('cabinet/save_recipient_template') }}" method="post">
        @csrf
        <div class="mb-4"><label class="text-gray-800 font-bold text-base inline-block mb-2">ФИО</label><input type="text" name="recipient_name" class="form-input w-full md:w-1/2" required></div>
        <div class="mb-4"><label class="text-gray-800 font-bold text-base inline-block mb-2">Телефон</label><input type="text" name="recipient_phone" class="form-input w-full md:w-1/2" required></div>
        <div class="mb-4"><label class="text-gray-800 font-bold text-base inline-block mb-2">Компания</label><input type="text" name="company" class="form-input w-full md:w-1/2"></div>
        <div class="mb-4"><label class="text-gray-800 font-bold text-base inline-block mb-2">Город</label><input type="text" name="city" class="form-input w-full md:w-1/2"></div>
        <div class="mb-4"><label class="text-gray-800 font-bold text-base inline-block mb-2">Страна</label><input type="text" name="country" class="form-input w-full md:w-1/2"></div>
        <div class="mb-4"><label class="text-gray-800 font-bold text-base inline-block mb-2">Область</label><input type="text" name="region" class="form-input w-full md:w-1/2"></div>
        <div class="mb-4"><label class="text-gray-800 font-bold text-base inline-block mb-2">Район</label><input type="text" name="district" class="form-input w-full md:w-1/2"></div>
        <div class="mb-4"><label class="text-gray-800 font-bold text-base inline-block mb-2">Адрес</label><textarea name="address" class="form-input w-full md:w-1/2" rows="3"></textarea></div>
        <button type="submit" class="common_btn">Сохранить отправителя</button>
    </form>
</main>
@endsection
