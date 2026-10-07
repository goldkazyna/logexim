@extends('layouts.app')

@push('styles')
<style>
    .searchCityDelivery { position: relative; }
    .searchCityDelivery ul { position: absolute; background: white; left:0px; width:100%; top:53px; z-index: 99999; box-shadow: 0px 0px 10px 0px rgba(34, 60, 80, 0.1) inset; border-radius: 2px; border: 1px solid #e9e9e9; margin: 0px; padding:0px; display: none; }
    .searchCityDelivery ul.active { display: block; }
    .searchCityDelivery ul li { padding: 20px; border: 1px solid #e9e9e9; cursor: pointer; }
    .searchCityDelivery ul li:hover { font-weight: bold; }
    .searchCityDelivery ul li:last-child { border:none; }

    /* Калькулятор доставки */
    .calc-hint { font-size: 14px; color: #666; margin: 0 0 12px; }
    .calc-check { display: inline-flex; align-items: center; gap: 10px; font-size: 15px; cursor: pointer; }
    .calc-check input { width: 18px; height: 18px; accent-color: #D0171C; }
    .calc-message { margin-top: 16px; padding: 12px 16px; border-radius: 10px; background: #fdecec; color: #a01215; font-size: 15px; }
    .calc-notes { margin: 16px 0 0; padding: 12px 16px 12px 34px; border-radius: 10px; background: #f4f6f8; font-size: 15px; }
    .calc-terms { margin: 20px 0 0; padding-left: 18px; font-size: 13px; color: #777; line-height: 1.6; }
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
        @include('partials.track-form')

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
                                <input type="number" name="package_weight" min="0.1" step="0.1" placeholder="" required>
                                <span class="form-text__desc form-text__desc_bottom">Фактический вес</span>
                            </label>
                        </div>
                        <p class="calc-hint">Габариты необязательны: если их указать, считаем по большему из весов — фактическому или объёмному (Д × Ш × В / 5000).</p>
                        <div class="form__row" id="volume">
                            <label class="form-text form_col-1">
                                <span class="form-text__desc form-text__desc_top">Длина (см)</span>
                                <input type="number" name="package_length" min="0" step="0.1" placeholder="">
                            </label>
                            <label class="form-text form_col-1">
                                <span class="form-text__desc form-text__desc_top">Ширина (см)</span>
                                <input type="number" name="package_width" min="0" step="0.1" placeholder="">
                            </label>
                            <label class="form-text form_col-1">
                                <span class="form-text__desc form-text__desc_top">Высота (см)</span>
                                <input type="number" name="package_height" min="0" step="0.1" placeholder="">
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
                        </div>
                        <div class="form__row">
                            <label class="calc-check">
                                <input type="checkbox" name="non_stackable" value="1">
                                Нештабелируемый груз (на него нельзя ставить другие грузы)
                            </label>
                        </div>
                        <div class="form__row form__row_end">
                            <div class="form-result form_col-4">
                                <span class="form-result__desc">Объемный вес:</span>
                                <div class="form-result__val_p calc-volume">-<span class="form-result__symb">кг</span></div>
                            </div>
                            <div class="form-result form_col-4">
                                <span class="form-result__desc">Расчётный вес:</span>
                                <div class="form-result__val_p calc-chargeable">-<span class="form-result__symb">кг</span></div>
                            </div>
                            <div class="form-result form_col-4">
                                <span class="form-result__desc">Срок доставки:</span>
                                <div class="form-result__val_p calc-time">-</div>
                            </div>
                        </div>
                        <div class="form__row form__row_end">
                            <div class="form-result form_col-4">
                                <span class="form-result__desc">Итого:</span>
                                <div class="form-result__val calc-total">-<span class="form-result__symb">₸</span></div>
                            </div>
                            <button type="submit" class="btn form__submit form_col-1">Рассчитать</button>
                        </div>
                        <div class="calc-message" hidden></div>
                        <ul class="calc-notes" hidden></ul>
                        <ul class="calc-terms">
                            <li>Цены с НДС 16%, доставка «от двери до двери».</li>
                            <li>Минимальный сбор — 9 800 ₸ за отправление до 20 кг, каждый следующий килограмм — по тарифу направления.</li>
                            <li>Негабарит (длина больше 3 м, ширина больше 2 м, высота больше 1,8 м или вес больше 500 кг) — коэффициент 1,3; нештабелируемый груз — коэффициент 2.</li>
                            <li>Отгрузка в регионы — по средам и пятницам. Грузы тяжелее 20 кг принимаем и доставляем до подъезда здания.</li>
                            <li>Погрузка и разгрузка — силами клиента, грузчиков организуем по запросу.</li>
                        </ul>
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
                        <a href="tel:+77273517341" class="btn"><i class="icon-mobile-phone"></i>+7 (727) 351 73 41</a>
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

    function formatNumber(number) {
        return new Intl.NumberFormat("ru-RU", { maximumFractionDigits: 2 }).format(number || 0);
    }

    // Расчёт целиком на сервере (App\Support\DeliveryCalculator) — здесь только показ.
    const calcForm = document.querySelector('form.package__form');
    const setVal = (sel, text) => { calcForm.querySelector(sel).childNodes[0].nodeValue = text; };
    const fieldNum = (name) => parseFloat(calcForm.querySelector(`input[name="${name}"]`).value) || 0;
    const transport = () => calcForm.querySelector('input[name="package_transport"]:checked').value;

    calcForm.addEventListener('submit', function(e){
        e.preventDefault();
        const from = calcForm.querySelector('input[name="package_from"]').dataset.city;
        const to = calcForm.querySelector('input[name="package_to"]').dataset.city;
        if (parseInt(from) === 0 || parseInt(to) === 0) { alert('Выберите города из списка'); return; }

        const message = calcForm.querySelector('.calc-message');
        const notes = calcForm.querySelector('.calc-notes');
        message.hidden = true; notes.hidden = true; notes.innerHTML = '';

        $.ajax({
            url: '/ajax/calcDelivery', method: 'post',
            data: {
                package_from: from, package_to: to, transport: transport(),
                weight: fieldNum('package_weight'),
                length: fieldNum('package_length'), width: fieldNum('package_width'), height: fieldNum('package_height'),
                non_stackable: calcForm.querySelector('input[name="non_stackable"]').checked ? 1 : 0,
            },
            success: (data) => {
                if (typeof data === 'string') data = JSON.parse(data.trim());
                setVal('.calc-volume', data.volume_weight ? formatNumber(data.volume_weight) + ' ' : '- ');
                setVal('.calc-chargeable', formatNumber(data.chargeable_weight) + ' ');
                if (!data.found) {
                    setVal('.calc-time', '-');
                    setVal('.calc-total', '- ');
                    message.textContent = 'По этому направлению стоимость рассчитывается индивидуально — позвоните нам: +7 771 775 57 13.';
                    message.hidden = false;
                    return;
                }
                setVal('.calc-time', data.time || '-');
                setVal('.calc-total', formatNumber(data.price) + ' ');
                const lines = (data.notes || []).slice();
                if (transport() !== 'air' && data.chargeable_weight > 20) {
                    lines.unshift('9 800 ₸ за первые 20 кг + ' + formatNumber(data.chargeable_weight - 20) + ' кг × ' + formatNumber(data.rate) + ' ₸');
                }
                if (lines.length) {
                    notes.innerHTML = lines.map(t => '<li>' + t + '</li>').join('');
                    notes.hidden = false;
                }
            },
            error: () => { alert('Проверьте вес и габариты груза'); }
        });
    });
})
</script>
@endpush
