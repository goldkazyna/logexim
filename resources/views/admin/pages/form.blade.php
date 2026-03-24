@extends('layouts.admin')
@section('title', 'Редактировать страницу')
@section('content')
<div class="card">
    <div class="card-header">{{ $page->title }}</div>
    <div class="card-body">
        <form action="{{ url('admin/pages/update/'.$page->id) }}" method="post">
            @csrf
            <div class="form-group"><label>Title (SEO)</label><input type="text" name="title" class="form-control" value="{{ $page->title }}" required></div>
            <div class="form-group"><label>Заголовок H1</label><input type="text" name="header" class="form-control" value="{{ $page->header }}" required></div>
            <div class="form-group"><label>Alias (URL)</label><input type="text" name="alias" class="form-control" value="{{ $page->alias }}" required></div>
            <div class="form-group"><label>Meta Title</label><input type="text" name="mt" class="form-control" value="{{ $page->mt }}"></div>
            <div class="form-group"><label>Meta Description</label><input type="text" name="md" class="form-control" value="{{ $page->md }}"></div>
            <div class="form-group"><label>Контент</label><textarea name="text" class="form-control" rows="10">{{ $page->text }}</textarea></div>
            <button type="submit" class="btn btn-primary">Сохранить</button>
        </form>
    </div>
</div>
@endsection
