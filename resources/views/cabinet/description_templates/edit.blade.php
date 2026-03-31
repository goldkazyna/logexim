@extends('layouts.cabinet')
@section('title', 'Редактировать шаблон описания')
@push('styles')
<style>.common_btn{background-color:#D0171C;border-color:#D0171C;border-radius:10px;color:#fff;padding:8px 20px;border:none;cursor:pointer}.common_btn:hover{background-color:#a21216}</style>
@endpush
@section('content')
<main class="flex-grow p-6">
    <h4 class="text-slate-900 text-lg font-medium mb-4">Редактировать шаблон описания</h4>
    <form action="{{ url('cabinet/update_description_template') }}" method="post">
        @csrf
        <input type="hidden" name="id" value="{{ $template->id }}">
        <div class="mb-4"><label class="text-gray-800 font-bold text-base inline-block mb-2">Название шаблона</label><input type="text" name="template_name" class="form-input w-full md:w-1/2" value="{{ $template->template_name }}" required></div>
        <div class="mb-4"><label class="text-gray-800 font-bold text-base inline-block mb-2">Описание вложения</label><input type="text" name="description" class="form-input w-full md:w-1/2" value="{{ $template->description }}"></div>
        <div class="mb-4"><label class="text-gray-800 font-bold text-base inline-block mb-2">Количество мест</label><input type="number" name="quantity" class="form-input w-full md:w-1/2" value="{{ $template->quantity }}"></div>
        <div class="mb-4"><label class="text-gray-800 font-bold text-base inline-block mb-2">Вес (кг)</label><input type="number" name="weight" class="form-input w-full md:w-1/2" value="{{ $template->weight }}" step="0.01"></div>
        <div class="mb-4"><label class="text-gray-800 font-bold text-base inline-block mb-2">Объёмный вес (кг)</label><input type="number" name="volume_weight" class="form-input w-full md:w-1/2" value="{{ $template->volume_weight }}" step="0.01"></div>
        <button type="submit" class="common_btn">Сохранить изменения</button>
    </form>
</main>
@endsection
