@extends('layouts.cabinet')
@section('title', 'Редактировать шаблон')
@push('styles')
<style>.common_btn{background-color:#D0171C;border-color:#D0171C;border-radius:10px;color:#fff;padding:8px 20px;border:none;cursor:pointer}.common_btn:hover{background-color:#a21216}</style>
@endpush
@section('content')
<main class="flex-grow p-6">
    <h4 class="text-slate-900 text-lg font-medium mb-4">Редактировать шаблон</h4>
    <form action="{{ url('cabinet/update_recipient_template') }}" method="post">
        @csrf
        <input type="hidden" name="id" value="{{ $template->id }}">
        <div class="mb-4"><label class="text-gray-800 font-bold text-base inline-block mb-2">ФИО</label><input type="text" name="recipient_name" class="form-input w-full md:w-1/2" value="{{ $template->recipient_name }}" required></div>
        <div class="mb-4"><label class="text-gray-800 font-bold text-base inline-block mb-2">Телефон</label><input type="text" name="recipient_phone" class="form-input w-full md:w-1/2" value="{{ $template->recipient_phone }}" required></div>
        <div class="mb-4"><label class="text-gray-800 font-bold text-base inline-block mb-2">Компания</label><input type="text" name="company" class="form-input w-full md:w-1/2" value="{{ $template->company }}"></div>
        <div class="mb-4"><label class="text-gray-800 font-bold text-base inline-block mb-2">Город</label><input type="text" name="city" class="form-input w-full md:w-1/2" value="{{ $template->city }}"></div>
        <div class="mb-4"><label class="text-gray-800 font-bold text-base inline-block mb-2">Страна</label><input type="text" name="country" class="form-input w-full md:w-1/2" value="{{ $template->country }}"></div>
        <div class="mb-4"><label class="text-gray-800 font-bold text-base inline-block mb-2">Область</label><input type="text" name="region" class="form-input w-full md:w-1/2" value="{{ $template->region }}"></div>
        <div class="mb-4"><label class="text-gray-800 font-bold text-base inline-block mb-2">Район</label><input type="text" name="district" class="form-input w-full md:w-1/2" value="{{ $template->district }}"></div>
        <div class="mb-4"><label class="text-gray-800 font-bold text-base inline-block mb-2">Адрес</label><textarea name="address" class="form-input w-full md:w-1/2" rows="3">{{ $template->address }}</textarea></div>
        <button type="submit" class="common_btn">Сохранить изменения</button>
    </form>
</main>
@endsection
