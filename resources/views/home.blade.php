@extends('layouts.app')

@push('styles')
<style>
    .order-form .search-status { display: none; }
    .order-form .search-status.active { display: block; }
    .searchCityDelivery { position: relative; }
    .searchCityDelivery ul { position: absolute; background: white; left:0px; width:100%; top:53px; z-index: 99999; box-shadow: 0px 0px 10px 0px rgba(34, 60, 80, 0.1) inset; border-radius: 2px; border: 1px solid #e9e9e9; margin: 0px; padding:0px; display: none; }
    .searchCityDelivery ul.active { display: block; }
    .searchCityDelivery ul li { padding: 20px; border: 1px solid #e9e9e9; cursor: pointer; }
    .searchCityDelivery ul li:hover { font-weight: bold; }
    .searchCityDelivery ul li:last-child { border:none; }
</style>
@endpush

@section('content')
<main class="main">
    <section class="hero">
        <video autoplay muted loop pip="false" class="hero__bg">
            <source src="{{ asset('assets/img/video-bg.mp4') }}" type="video/mp4">
            Ваш браузер не поддерживает воспроизведение видео.
        </video>
        <div class="container">
            <div class="hero__content">
                <h1 class="hero__title">Быстрая и&nbsp;надёжная <span>доставка грузов по&nbsp;Казахстану</span></h1>
                <div class="hero__desc">В кратчайшие сроки, независимо&nbsp;от&nbsp;сложности</div>
            </div>
            <div class="hero__scroll"><i class="icon-arrow"></i></div>
        </div>
    </section>

    <section class="services">
        <div class="container">
            <div class="services__wrapper">
                <div class="services__item">
                    <i class="icon-loading"></i>
                    <div class="services__description">
                        <h3>Перевозка грузов</h3>
                        <p>Мы готовы перевести груз весом от&nbsp;<b>1&nbsp;кг&nbsp;до&nbsp;200&nbsp;тонн</b></p>
                    </div>
                </div>
                <div class="services__item">
                    <i class="icon-product"></i>
                    <div class="services__description">
                        <h3>Офисный переезд</h3>
                        <p>Мы знаем как с <b>минимальными затратами</b> перевести офис любой площади</p>
                    </div>
                </div>
                <div class="services__item">
                    <i class="icon-warehouse"></i>
                    <div class="services__description">
                        <h3>Автоконсолидация</h3>
                        <p>Доставка сборных грузов по <b>всему Казахстану</b></p>
                    </div>
                </div>
                <div class="services__item">
                    <i class="icon-movers"></i>
                    <div class="services__description">
                        <h3>Погрузочно-разгрузочные работы</h3>
                        <p>Производятся <b>профессиональными</b> грузчиками и&nbsp;такелажниками</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="grey-bg">
        <section class="search">
            <div class="container">
                <div class="search__wrapper">
                    <form class="search__form form order-form">
                        <h2>Найти посылку</h2>
                        <div class="form__row">
                            <label class="form-text form_col-6">
                                <span class="form-text__desc form-text__desc_top">Введите трек номер:</span>
                                <input type="text" name="search_track" data-mask="track" placeholder="______" required>
                            </label>
                            <button type="submit" class="btn form__submit form_col-4">Продолжить</button>
                        </div>
                        <div class="search-result">
                            <span class="_error" style="display: none;">По вашему трек номеру нет информации</span>
                            <div class="search-status" data-current="50">
                                <div class="search-status__line">
                                    <div class="search-status__active"></div>
                                    <div class="search-status__start">
                                        <span class="search-status__label"><i class="icon-warehouse-fill"></i></span>
                                        <span class="search-status__desc">Отправлено: <span></span></span>
                                        <span class="search-status__desc">Отправитель: <span></span></span>
                                    </div>
                                    <div class="search-status__current">
                                        <span class="search-status__desc">В пути</span>
                                        <span class="search-status__label"><i class="icon-truck-fill"></i></span>
                                    </div>
                                    <div class="search-status__end">
                                        <span class="search-status__label"><i class="icon-finish_fill"></i></span>
                                        <span class="search-status__desc date_to">Ожидает доставки <span></span></span>
                                        <span class="search-status__desc name_to">Получатель: <span></span></span>
                                        <span class="search-status__desc success_delivery" style="display:none;color: green; font-weight: bold;">Доставлено</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </section>

        <section class="package">
            <div class="container">
                <div class="package__wrapper">
                    <form class="package__form form">
                        <h2>Рассчитать стоимость доставки груза</h2>
                        <div class="form__row">
                            <div class="form__swap form_col-9">
                                <label class="form-text searchCityDelivery">
                                    <span class="form-text__desc form-text__desc_top">Откуда</span>
                                    <input autocomplete="off" type="text" name="package_from" placeholder="Пункт отправления" data-city="0">
                                    <ul></ul>
                                    <span class="form-text__desc form-text__desc_bottom"><b>Например:</b> Алматы, Астана, Шымкент</span>
                                </label>
                                <button class="swap"><i class="icon-swap"></i></button>
                                <label class="form-text searchCityDelivery">
                                    <span class="form-text__desc form-text__desc_top">Куда</span>
                                    <input autocomplete="off" type="text" name="package_to" placeholder="Пункт назначения" data-city="0">
                                    <ul></ul>
                                    <span class="form-text__desc form-text__desc_bottom"><b>Например:</b> Шымкент, Астана, Алматы</span>
                                </label>
                            </div>
                            <label class="form-text form_col-1">
                                <span class="form-text__desc form-text__desc_top">Масса (кг)</span>
                                <input type="number" name="package_weight" min="1" step="0.1" placeholder="" required>
                                <span class="form-text__desc form-text__desc_bottom">От 1 кг</span>
                            </label>
                        </div>
                        <div class="form__row calc_type">
                            <label class="form-radio">
                                <input type="radio" name="calc_type" value="weight" checked>
                                <span class="form-radio__label"></span>
                                <span class="form-radio__desc">Считать по весу</span>
                            </label>
                            <label class="form-radio">
                                <input type="radio" name="calc_type" value="volume">
                                <span class="form-radio__label"></span>
                                <span class="form-radio__desc">Считать по объему</span>
                            </label>
                        </div>
                        <div class="form__row" id="volume">
                            <label class="form-text form_col-1">
                                <span class="form-text__desc form-text__desc_top">Длина (см)</span>
                                <input disabled type="number" name="package_length" min="1" step="0.1" placeholder="" required="">
                            </label>
                            <label class="form-text form_col-1">
                                <span class="form-text__desc form-text__desc_top">Ширина (см)</span>
                                <input disabled type="number" name="package_width" min="1" step="0.1" placeholder="" required="">
                            </label>
                            <label class="form-text form_col-1">
                                <span class="form-text__desc form-text__desc_top">Высота (см)</span>
                                <input disabled type="number" name="package_height" min="1" step="0.1" placeholder="" required="">
                            </label>
                        </div>
                        <div class="form__row">
                            <label class="form-radio">
                                <input type="radio" name="package_transport" value="car" checked>
                                <span class="form-radio__label"></span>
                                <span class="form-radio__desc">Автодоставка</span>
                            </label>
                            <label class="form-radio">
                                <input type="radio" name="package_transport" value="air">
                                <span class="form-radio__label"></span>
                                <span class="form-radio__desc">Авиадоставка</span>
                            </label>
                            <label class="form-radio">
                                <input type="radio" name="package_transport" value="railway">
                                <span class="form-radio__label"></span>
                                <span class="form-radio__desc">ЖД доставка</span>
                            </label>
                            <span class="_error" style="display:none;">По данному направлению нету маршрутов</span>
                        </div>
                        <div class="form__row form__row_end">
                            <div class="form-result form_col-4">
                                <span class="form-result__desc">Объемный вес:</span>
                                <div class="form-result__val_p"><span class="form-result__symb">кг</span></div>
                            </div>
                        </div>
                        <div class="form__row form__row_end">
                            <div class="form-result form_col-4">
                                <span class="form-result__desc">Итого:</span>
                                <div class="form-result__val">-<span class="form-result__symb">₸</span></div>
                            </div>
                            <button type="submit" class="btn form__submit form_col-1">Рассчитать</button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>

    <section class="about">
        <div class="container">
            <h2>О компании</h2>
            <div class="about__content">
                <div class="about__text">
                    <p>Компания «LogExim Express» выражает Вам и Вашей компании глубокое уважение и благодарность за интерес к нашим услугам.</p>
                    <p>Мы специализируемся на автогрузоперевозках и за годы работы накопили обширный опыт в доставке разнообразных грузов по Казахстану. Наша компания постоянно развивается, расширяя спектр услуг, повышая качество сервиса и увеличивая географию перевозок.</p>
                    <p>Благодаря профессионализму команды и современным технологиям, мы гарантируем оперативность, безопасность и индивидуальный подход к каждому клиенту. Мы уверены, что наше сотрудничество будет взаимовыгодным и плодотворным!</p>
                </div>
                <div class="about__img">
                    <img src="{{ asset('assets/img/driver.jpg') }}" alt="О компании">
                </div>
            </div>
        </div>
    </section>

    <section class="news">
        <div class="container">
            <div class="news__wrapper">
                <h2>Новости</h2>
                <div class="news__list">
                    @foreach($news as $item)
                    <div class="news__item">
                        <h3>{{ $item->title }}</h3>
                        <p class="news__excerpt" style="height: 156px;">{{ $item->discription }}</p>
                        <div class="news__meta">
                            <span class="news__date">{{ \Carbon\Carbon::parse($item->date)->format('d.m.Y') }}</span>
                            <a class="news__link" href="/news/detail/{{ $item->id }}">Читать публикацию</a>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

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

    <section class="faq">
        <div class="container">
            <h2>Часто задаваемые вопросы</h2>
            <div class="faq__wrapper">
                <div class="faq__item">
                    <div class="faq__question">
                        <h3>Как часто Ваша компания производит отправку грузов?</h3>
                        <span class="faq__icon icon-angle"></span>
                    </div>
                    <div class="faq__answer">
                        <p>Наша компания осуществляет регулярные отправки грузов два раза в неделю. Мы отправляем грузы каждую среду и пятницу.</p>
                    </div>
                </div>
                <div class="faq__item">
                    <div class="faq__question">
                        <h3>Возможно ли оплатить доставку груза после получения?</h3>
                        <span class="faq__icon icon-angle"></span>
                    </div>
                    <div class="faq__answer">
                        <p>Да, такая возможность предусмотрена. Мы предлагаем удобную опцию оплаты доставки груза после его получения.</p>
                    </div>
                </div>
                <div class="faq__item">
                    <div class="faq__question">
                        <h3>Какие виды грузов вы перевозите?</h3>
                        <span class="faq__icon icon-angle"></span>
                    </div>
                    <div class="faq__answer">
                        <p>Мы перевозим практически все виды грузов, включая промышленные товары, строительные материалы, бытовую технику, мебель, текстиль. Мы не осуществляем перевозки овощей, фруктов и опасных грузов.</p>
                    </div>
                </div>
                <div class="faq__item">
                    <div class="faq__question">
                        <h3>В каких регионах Казахстана вы осуществляете перевозки?</h3>
                        <span class="faq__icon icon-angle"></span>
                    </div>
                    <div class="faq__answer">
                        <p>Наша компания осуществляет перевозки грузов по всему Казахстану, охватывая все регионы страны.</p>
                    </div>
                </div>
                <div class="faq__item">
                    <div class="faq__question">
                        <h3>Предоставляете ли вы услуги страхования груза?</h3>
                        <span class="faq__icon icon-angle"></span>
                    </div>
                    <div class="faq__answer">
                        <p>Да, мы предоставляем услуги страхования груза. Мы сотрудничаем с надежными страховыми компаниями.</p>
                    </div>
                </div>
                <div class="faq__item">
                    <div class="faq__question">
                        <h3>Можно ли отслеживать местоположение груза?</h3>
                        <span class="faq__icon icon-angle"></span>
                    </div>
                    <div class="faq__answer">
                        <p>В ближайшее время мы планируем запустить функцию отслеживания местоположения груза.</p>
                    </div>
                </div>
                <div class="faq__item">
                    <div class="faq__question">
                        <h3>Какие типы транспорта вы используете для перевозок?</h3>
                        <span class="faq__icon icon-angle"></span>
                    </div>
                    <div class="faq__answer">
                        <p>Автотранспорт, авиационный транспорт и железнодорожный транспорт.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="contacts">
        <div class="container">
            <div class="contacts__wrapper">
                <div class="contacts__content">
                    <h2>Контакты</h2>
                    <p class="contacts__desc">Адрес: Республика Казахстан, город Алматы, Нурмакова 1/1, офис №407</p>
                    <p class="contacts__desc">Адрес склада: трасса Алматы-Усть-Каменогорск, ул, Аксуат 110</p>
                    <div class="contacts__action">
                        <a href="tel:+77072301565" class="btn"><i class="icon-mobile-phone"></i>+7 (707) 230 15 65</a>
                        <a href="tel:+77273209669" class="btn"><i class="icon-mobile-phone"></i>+7 (727) 320 96 69</a>
                        <a href="mailto:info@logeximexpress.kz" class="btn"><i class="icon-email"></i>info@logeximexpress.kz</a>
                    </div>
                </div>
            </div>
            <div class="contacts__map">
                <script type="text/javascript" charset="utf-8" async src="https://api-maps.yandex.ru/services/constructor/1.0/js/?um=constructor%3A8e31eb6be942906d8a7118471d5d786c59a0978e515590c013f4de248ba3a5be&amp;width=100%25&amp;&amp;lang=ru_RU&amp;scroll=true"></script>
            </div>
        </div>
    </section>
</main>
@endsection

@push('scripts')
<script>
window.addEventListener('load', function(){
    const calc_type = document.querySelector('.calc_type');
    const volume = document.querySelector('#volume');
    const fieldPackageWeight = document.querySelector('input[name="package_weight"]');
    if(calc_type && volume){
        calc_type.addEventListener('change', function(e){
            if(e.target.closest('input[name]')){
                let value = e.target.closest('input[name]').value;
                if(value === 'volume'){
                    volume.querySelectorAll('input').forEach(node => node.disabled = false);
                    fieldPackageWeight.disabled = true;
                }else{
                    volume.querySelectorAll('input').forEach(node => node.disabled = true);
                    fieldPackageWeight.disabled = false;
                }
            }
        })
    }

    document.querySelector('input[data-mask="track"]').addEventListener('input', function(){
        let value = this.value.trim().replace(/[^0-9]{1,}/g, '')
        value = value.slice(0, 6);
        this.value = value;
    })

    document.querySelector('input[name="package_from"]').addEventListener('input', function(){ autocompleteCity.call(this); })
    document.querySelector('input[name="package_to"]').addEventListener('input', function(){ autocompleteCity.call(this); })

    function autocompleteCity(){
        let value = this.value.trim()
        if(value.length >= 3){
            $.ajax({
                url: '/ajax/searchCityDelivery',
                method: 'post',
                data: {search: value},
                success: (res) => {
                    if(typeof res === 'string') res = JSON.parse(res.trim());
                    let citys = res;
                    let id_city = citys.filter(item => item.title.trim().toLowerCase() === value.trim().toLowerCase()).map(item => item.id).join('')
                    if(id_city !== '') this.dataset.city = id_city;
                    if(citys.length > 0){
                        this.closest('.searchCityDelivery').querySelector('ul').innerHTML = citys.map(item => `<li data-id="${item.id}">${item.title}</li>`).join('')
                        this.closest('.searchCityDelivery').querySelector('ul').classList.add('active')
                    }else{
                        this.closest('.searchCityDelivery').querySelector('ul').classList.remove('active')
                    }
                }
            })
        }else{
            this.closest('.searchCityDelivery').querySelector('ul').classList.remove('active')
        }
    }

    document.querySelectorAll('.searchCityDelivery').forEach(node => {
        node.querySelector('input').addEventListener('blur', function(){
            setTimeout(() => { this.closest('.searchCityDelivery').querySelector('ul').classList.remove('active') }, 300)
        })
        node.querySelector('ul').addEventListener('click', function(e){
            if(e.target.closest('li')){
                this.closest('.searchCityDelivery').querySelector('input').dataset.city = e.target.closest('li').dataset.id;
                this.closest('.searchCityDelivery').querySelector('input').value = e.target.closest('li').innerText.trim();
                this.closest('.searchCityDelivery').querySelector('ul').classList.remove('active')
            }
        })
    })

    document.querySelector('form.package__form').addEventListener('click', function(e){
        if(e.target.closest('.swap')){
            let package_from = document.querySelector('input[name="package_from"][data-city]').dataset.city;
            let package_to = document.querySelector('input[name="package_to"][data-city]').dataset.city;
            document.querySelector('input[name="package_from"][data-city]').dataset.city = package_to;
            document.querySelector('input[name="package_to"][data-city]').dataset.city = package_from;
        }
    })

    document.querySelector('form.package__form').addEventListener('submit', function(e){
        e.preventDefault();
        let package_from = document.querySelector('input[name="package_from"][data-city]').dataset.city;
        let package_to = document.querySelector('input[name="package_to"][data-city]').dataset.city;
        let params = {};
        if(document.querySelector('input[name="package_weight"]:not(:disabled)')){
            params['package_weight'] = parseFloat(document.querySelector('input[name="package_weight"]').value);
        }else{
            params['package_length'] = parseFloat(document.querySelector('input[name="package_length"]').value);
            params['package_width'] = parseFloat(document.querySelector('input[name="package_width"]').value);
            params['package_height'] = parseFloat(document.querySelector('input[name="package_height"]').value);
        }
        let package_transport = document.querySelector('input[name="package_transport"]:checked').value.trim();
        if(parseInt(package_from) === 0 || parseInt(package_to)=== 0){ alert('Выберите города!'); return false; }
        if(package_transport === 'air') Air(package_from, package_to, params);
        else if(package_transport === 'railway') Railway(package_from, package_to, params);
        else if(package_transport === 'car') Car(package_from, package_to, params);
    })

    function formatNumber(number) {
        let n = new Intl.NumberFormat("ru-RU").format(number);
        return String(n).trim() === "не число" ? 0 : n;
    }

    function Railway(package_from, package_to, params){
        $.ajax({
            url: '/ajax/calcDeliveryZd', method: 'post',
            data: {package_from, package_to},
            success: (res) => {
                if(typeof res === 'string') res = JSON.parse(res.trim());
                let data = res;
                if(!('package_weight' in params)){
                    params['package_weight'] = (params['package_length'] * params['package_width'] * params['package_height'])/6000;
                    document.querySelector('.form-result__val_p').textContent = formatNumber(params['package_weight']);
                }
                if(data.price){
                    if(params.package_weight <= 20){ document.querySelector('.form-result__val').childNodes[0].nodeValue = formatNumber(6500); return false; }
                    let price = 6500;
                    if(params.package_weight > 20){ params.package_weight = params.package_weight - 20; price += params.package_weight * parseFloat(data.price); }
                    document.querySelector('.form-result__val').childNodes[0].nodeValue = formatNumber(price);
                }else{
                    document.querySelector('.form-result__val').childNodes[0].nodeValue = formatNumber(0);
                    alert('По такому направлению не возим!');
                }
            }
        })
    }

    function Car(package_from, package_to, params){
        $.ajax({
            url: '/ajax/calcDeliveryCar', method: 'post',
            data: {package_from, package_to},
            success: (res) => {
                if(typeof res === 'string') res = JSON.parse(res.trim());
                let data = res;
                if(!('package_weight' in params)){
                    params['package_weight'] = (params['package_length'] * params['package_width'] * params['package_height'])/6000;
                    document.querySelector('.form-result__val_p').textContent = formatNumber(params['package_weight']);
                }
                if(data.price){
                    if(params['package_weight'] <= 20){ document.querySelector('.form-result__val').childNodes[0].nodeValue = formatNumber(6500); return false; }
                    let price = 6500;
                    if(params['package_weight'] > 20){ params['package_weight'] = params['package_weight'] - 20; price += params['package_weight'] * parseFloat(data.price); }
                    document.querySelector('.form-result__val').childNodes[0].nodeValue = formatNumber(price);
                }else{
                    let price = 8500;
                    if(params['package_weight'] > 20){ params['package_weight'] = params['package_weight'] - 20; price += params['package_weight'] * 200; }
                    document.querySelector('.form-result__val_p').textContent = formatNumber(params['package_weight']);
                    document.querySelector('.form-result__val').childNodes[0].nodeValue = formatNumber(price);
                }
            }
        })
    }

    function Air(package_from, package_to, params){
        $.ajax({
            url: '/ajax/calcDeliveryAir', method: 'post',
            data: {package_from, package_to},
            success: (res) => {
                if(typeof res === 'string') res = JSON.parse(res.trim());
                let data = res;
                if(!('package_weight' in params)){
                    params['package_weight'] = (params['package_length'] * params['package_width'] * params['package_height'])/6000;
                    document.querySelector('.form-result__val_p').textContent = formatNumber(params['package_weight']);
                }
                if(data.price){
                    let price = parseInt(data.price);
                    if(params['package_weight'] > 3){ params['package_weight'] = params['package_weight'] - 3; price += params['package_weight'] * 850; }
                    document.querySelector('.form-result__val').childNodes[0].nodeValue = formatNumber(price);
                }else{
                    let price = 15000;
                    if(params['package_weight'] > 3){ params['package_weight'] = params['package_weight'] - 3; price += params['package_weight'] * 1200; }
                    document.querySelector('.form-result__val').childNodes[0].nodeValue = formatNumber(price);
                }
            }
        })
    }

    document.querySelector('form.order-form').addEventListener('submit', function(e){
        e.preventDefault();
        this.querySelector('.search-result ._error').style = 'display:none;';
        this.querySelector('.search-result .search-status').classList.remove('active');
        let search_track = this.querySelector('input[name="search_track"]').value.trim();
        if(search_track.length < 6) return false;
        $.ajax({
            url: '/ajax/searchTrack', method: 'post',
            data: {search_track},
            success: (res) => {
                if(typeof res === 'string') res = JSON.parse(res.trim());
                let data = res;
                if(data.id && new Date().getTime() > new Date(data.date_from).getTime()){
                    let block_status = this.querySelector('.search-result .search-status');
                    block_status.querySelector('.search-status__start .search-status__desc span').innerText = new Date(data.date_from).toLocaleDateString();
                    block_status.querySelector('.search-status__start .search-status__desc:last-child span').innerText = data.name_from;
                    block_status.querySelector('.search-status__end .date_to span').innerText = new Date(data.date_to).toLocaleDateString();
                    block_status.querySelector('.search-status__end .name_to span').innerText = data.name_to;
                    block_status.querySelector('.success_delivery').style.display = 'none';
                    if(parseInt(data.status) === 1){
                        showStatusDelivery(100);
                        block_status.querySelector('.success_delivery').style.display = 'inline-block';
                    }else{
                        let totalTime = new Date(data.date_to).getTime() - new Date(data.date_from).getTime();
                        let currTime = new Date().getTime() - new Date(data.date_from).getTime();
                        totalTime = totalTime / 1000 / 3600;
                        currTime = currTime / 1000 / 3600;
                        let percent = currTime / totalTime * 100;
                        showStatusDelivery(percent);
                    }
                    block_status.classList.add('active');
                }else{
                    this.querySelector('.search-result ._error').style = '';
                }
            }
        })
    })
})
</script>
@endpush
