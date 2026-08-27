@extends('layouts.app')

@section('title', $page->title . ' - Logexim Express')

@push('styles')
<style>
.header { background-color: #000000 !important; position: inherit !important; }
.container { width:100%; }
.header-bottom { padding: 20px 0 33px; }
.content { margin-top:20px; }
@media (max-width:767px) { .header-bottom { padding: 0px 0 0px; } }
body.main-page .header { position: relative !important; }
.header_contact { background-color: #f3f3f3; color: #000000; position: relative; text-transform: uppercase; width: 75%; max-width: 500px; box-sizing: border-box; padding: .3em 1em; overflow: visible; font-weight: normal; letter-spacing: 2px; margin-bottom: 1em; }
.set-2 { width: 450px; }
.header_contact:after { content: ''; display: block; position: absolute; top: 0; right: -35px; width: 0; height: 0; border-top: 0px solid transparent; border-bottom: 32px solid transparent; border-left: 35px solid #f3f3f3; }
@media (max-width: 767px) { .set-2 { width:300px !important; } }
</style>
@endpush

@section('content')
<section class="main-section" style="min-height:1000px">
    <br /><br />
    <div class="container cont">
        <div class="row" style="margin: 0px;">
            <div class="col-xs-12 col-lg-12" style="padding:0px; padding-right:35px">
                <div class="header_contact">Наши филиалы</div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-xs-12" style="margin-bottom: 30px;">
                <p style="font-size: 16px; font-weight: bold;">Алматы</p>
                <hr />
                <br />
                <p style="font-size: 14px; line-height: 1.5;">
                    <b>Адрес:</b> Республика Казахстан, Алматы, Ул. Коммунальная 3А<br />
                    <b>Адрес склада</b> трасса Алматы-Усть-Каменогорск, ул, Аксуат 110<br />
                    <b>Телефон:</b> +7 (707) 230 15 65, +7 (727) 351 73 41<br />
                    <b>E-mail:</b> sale@logexim.kz
                </p>
            </div>
        </div>
    </div>
</section>
@endsection
