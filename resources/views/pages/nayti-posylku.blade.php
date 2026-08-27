@extends('layouts.app')

@section('title', $page->title . ' - Logexim Express')

@push('styles')
<style>
.header { background-color: #000000 !important; position: inherit !important; }
.container { width:100%; }
.header-bottom { padding: 20px 0 33px; }
.content { margin-top:20px; }
@media (max-width:767px) { .header-bottom { padding: 0px 0 0px; } }
</style>
@endpush

@section('content')
<div class="container content">
    <div class="row">
        <div class="col-xs-12 col-lg-12">
            <div class="breadcrumbs">
                <span><a href="/">Главная</a></span> - <span>{{ $page->header }}</span>
            </div>
            <br />
            @include('partials.track-form')
        </div>
    </div>
</div>
@endsection
