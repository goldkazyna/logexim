@extends('layouts.app')

@section('title', $news->title . ' - Logexim Express')

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
    <div>
        <h1 align="center">{{ $news->title }}</h1>
    </div>
</section>
<br /><br />
<div class="container">
    <div class="content">
        @if($news->img)
        <div style="text-align: center;">
            <img class="itsimg" src="/{{ $news->img }}" style="max-width: 1100px;"/>
        </div>
        @endif
        <br />
        <div style="line-height: 1.5;">{!! $news->text !!}</div>
    </div>
</div>
<br /><br />
@endsection
