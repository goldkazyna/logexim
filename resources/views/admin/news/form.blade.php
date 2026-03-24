@extends('layouts.admin')
@section('title', isset($item) ? 'Редактировать новость' : 'Добавить новость')
@section('content')
<div class="card">
    <div class="card-header">{{ isset($item) ? 'Редактировать' : 'Новая новость' }}</div>
    <div class="card-body">
        <form action="{{ isset($item) ? url('admin/news/update/'.$item->id) : url('admin/news/store') }}" method="post">
            @csrf
            <div class="form-group"><label>Заголовок</label><input type="text" name="title" class="form-control" value="{{ $item->title ?? '' }}" required></div>
            <div class="form-group"><label>Описание</label><textarea name="discription" class="form-control" rows="3">{{ $item->discription ?? '' }}</textarea></div>
            <div class="form-group"><label>Текст</label><textarea name="text" class="form-control" rows="8">{{ $item->text ?? '' }}</textarea></div>
            <div class="form-group"><label>Изображение (путь)</label><input type="text" name="img" class="form-control" value="{{ $item->img ?? '' }}"></div>
            <div class="form-group"><label>Дата</label><input type="datetime-local" name="date" class="form-control" value="{{ isset($item) ? date('Y-m-d\TH:i', strtotime($item->date)) : date('Y-m-d\TH:i') }}"></div>
            <button type="submit" class="btn btn-primary">Сохранить</button>
        </form>
    </div>
</div>
@endsection
