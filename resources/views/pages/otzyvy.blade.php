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
<main>
    <section class="reviews">
        <div class="container">
            <h2>Отзывы</h2>
        </div>
        <div class="reviews__slider swiper" id="sliderReviews">
            <div class="swiper-wrapper">
                <article class="swiper-slide review">
                    <p class="review__text"></p>
                    <p class="review__full" data-length="30">Перевозили офис вместе с этой компанией до праздников. Понравилась оперативность работы, а также цены. Невысокие, но уровень сервиса высокий. Молодцы!</p>
                    <div class="review__meta">
                        <span class="review__about">Ануар | 27.05.2022</span>
                        <button class="btn btn_sm review__more">Читать отзыв</button>
                    </div>
                </article>
                <article class="swiper-slide review">
                    <p class="review__text"></p>
                    <p class="review__full" data-length="30">Мне компания помогла перевезти вещи. Терпеливые ребята. Вначале все упаковывали, потом грузили на машину. Доставили без поломок. Ребятки, побольше бы таких хороших людей как вы!</p>
                    <div class="review__meta">
                        <span class="review__about">Анна Михайловна | 27.05.2022</span>
                        <button class="btn btn_sm review__more">Читать отзыв</button>
                    </div>
                </article>
                <article class="swiper-slide review">
                    <p class="review__text"></p>
                    <p class="review__full" data-length="30">Сотрудничаем с компанией в течение года. Перевозим канцелярские товары по городу и регионы. Ни одного сбоя, ни одной задержки. Все аккуратно и вовремя. Благодарю за профессионализм.</p>
                    <div class="review__meta">
                        <span class="review__about">Антон | 27.05.2022</span>
                        <button class="btn btn_sm review__more">Читать отзыв</button>
                    </div>
                </article>
            </div>
        </div>
    </section>
</main>
@endsection
