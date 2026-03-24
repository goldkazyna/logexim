@extends('layouts.app')

@section('title', $page->title . ' - Logexim Express')

@section('content')
<main class="main">
    <section class="page-content">
        <div class="container">
            <h1>{{ $page->header }}</h1>
            <div class="page-text">
                {!! $page->text !!}
            </div>
        </div>
    </section>
</main>
@endsection
