@extends('layouts.app')

@section('title', 'Новости - Logexim Express')

@push('styles')
<style>
.header { background-color: #000000 !important; position: inherit !important; }
.container { width:100%; }
.header-bottom { padding: 20px 0 33px; }
.content { margin-top:20px; }
h1 { font-size:30px !important; font-weight: bold; margin:20px 0px; }
.news_title { font-size:20px; font-weight: bold; }
hr { border-top:1px solid #cccccc; }
@media (max-width:767px) { .header-bottom { padding: 0px 0 0px; } }
</style>
@endpush

@section('content')
<section class="photo-page">
    <div align="center">
        <h1>Новости</h1>
        <p style="line-height: 1.5;">Будь в курсе событий: читай наши новости и оставайся на шаг впереди!</p>
    </div>
</section>
<br /><br />
<div class="wa" style="min-height: 500px;">
    <div class="container">
        @foreach($news as $item)
        <div class="row" style="overflow: hidden;">
            <div class="col-lg-7">
                <div class="news_title" style="text-transform: uppercase; line-height: 1.5;">
                    <a href="/news/detail/{{ $item->id }}">{{ $item->title }}</a>
                </div>
                <br />
                <div class="news_discription" style="line-height: 1.5;">{{ $item->discription }}</div>
            </div>
        </div>
        <br /><hr /><br />
        @endforeach
    </div>
</div>
@endsection
