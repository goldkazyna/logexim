@extends('layouts.cabinet')
@section('title', 'Добавить шаблон описания')
@push('styles')
<style>.common_btn{background-color:#D0171C;border-color:#D0171C;border-radius:10px;color:#fff;padding:8px 20px;border:none;cursor:pointer}.common_btn:hover{background-color:#a21216}</style>
@endpush
@section('content')
<main class="flex-grow p-6">
    <h4 class="text-slate-900 text-lg font-medium mb-4">Добавить шаблон описания</h4>
    <form action="{{ url('cabinet/save_description_template') }}" method="post">
        @csrf
        <div class="mb-4"><label class="text-gray-800 font-bold text-base inline-block mb-2">Название шаблона</label><input type="text" name="template_name" class="form-input w-full md:w-1/2" required></div>
        <div class="mb-4"><label class="text-gray-800 font-bold text-base inline-block mb-2">Описание вложения</label><input type="text" name="description" class="form-input w-full md:w-1/2"></div>
        <div class="mb-4"><label class="text-gray-800 font-bold text-base inline-block mb-2">Количество мест</label><input type="number" name="quantity" class="form-input w-full md:w-1/2"></div>
        <div class="mb-4"><label class="text-gray-800 font-bold text-base inline-block mb-2">Вес (кг)</label><input type="number" name="weight" class="form-input w-full md:w-1/2" step="0.01"></div>
        <div class="mb-4"><label class="text-gray-800 font-bold text-base inline-block mb-2">Объёмный вес (кг)</label><input type="number" name="volume_weight" class="form-input w-full md:w-1/2" step="0.01"></div>
        <button type="submit" class="common_btn">Сохранить шаблон</button>
    </form>
</main>
@endsection
