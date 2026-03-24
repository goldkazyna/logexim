@extends('layouts.app')

@section('title', $page->title . ' - Logexim Express')

@push('styles')
<style>
.header { background-color: #000000 !important; position: inherit !important; }
.container { width:100%; }
.header-bottom { padding: 20px 0 33px; }
.content { margin-top:20px; }
h2 { font-size:24px; font-weight: bold; margin: 20px 0px; }
hr { border-top: 1px solid #cccccc !important; }
.text { font-size:14px; line-height: 1.5; }
.text_ul { font-size:14px; line-height: 1.5; }
.text_ul li { list-style: disc; margin-left:50px; }
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
            <h1 style="font-size: 36px;">{{ $page->header }}</h1>
            <hr />
            <br />
            <img src="/images/images/1722954483PJsp9qUr4lgt2cq3.jpg" width="100%"/>
            <br />
            <h2>Компания TOO «LOGEXIM EXPRESS»</h2>
            <br />
            <p class="text">выражает Вам и Вашей компании свое почтение и благодарит Вас за интерес, проявленный к нашим услугам.

Основное направление нашей компании это авто-грузоперевозки. За время существования мы накопили большой опыт доставки различных грузов по территории Казахстана. Компания постоянно растет и развивается, расширяя спектр своих возможностей, повышая качество сервиса оказываемых услуг и географию перевозок.</p>
            <h2>ПРЕИМУЩЕСТВА</h2>
            <ul class="text_ul">
                <li>специалисты компании проконсультируют Вас по вопросам организации перевозок, помогут подобрать тип транспорта и маршрут</li>
                <li>стабильное качество услуг – своевременная и точная доставка грузов, полный комплект документов по выполненным перевозкам</li>
                <li>оптимальный тариф</li>
                <li>отлаженная система взаимодействия с Клиентами.</li>
            </ul>
            <br />
            <h2>Заключение</h2>
            <br />
            <p class="text">Перевозка грузов - это неотъемлемая часть любого бизнеса, требующая внимательного выбора провайдера услуг. Правильный выбор поможет обеспечить эффективность, безопасность и экономичность вашей логистики. Учитывайте разнообразие услуг, ценовую политику и стандарты безопасности при выборе провайдера перевозочных услуг, чтобы обеспечить успешное развитие вашего бизнеса.</p>
        </div>
    </div>
</div>
@endsection
