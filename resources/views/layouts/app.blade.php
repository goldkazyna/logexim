<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('assets/libs/fancybox/fancybox.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/libs/swiper/swiper-bundle.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/main.css') }}?t={{ time() }}">
    <title>@yield('title', 'Logexim Express - Быстрая и надёжная доставка грузов по Казахстану')</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    @stack('styles')
</head>
<body class="">

<div class="modal-panel">
    <div class="modal-panel__overlay"></div>
    <div class="modal-panel__content">
        <a class="btn-burger _active" href="#">
            <div class="btn-burger__icon">
                <div class="btn-burger__line btn-burger__line_1"></div>
                <div class="btn-burger__line btn-burger__line_2"></div>
                <div class="btn-burger__line btn-burger__line_3"></div>
                <div class="btn-burger__line btn-burger__line_4"></div>
            </div>
        </a>
        <div class="modal-panel__header">
            <a href="/" class="logo">
                <img src="/images/new-logo.png" alt="Logexim Express" class="logo__icon">
            </a>
        </div>

        <div class="modal-panel__contacts">
            <a href="tel:+77072301565"><i class="icon-mobile-phone"></i>+7 (707) 230 15 65</a>
            <a href="tel:+77273517341"><i class="icon-old-phone"></i>+7 (727) 351 73 41</a>
        </div>

        <nav class="nav-modal">
            <ul class="nav-modal__menu">
                <li class="nav-modal__item has-menu">
                    <a href="#" class="nav-modal__link">Услуги<span class="nav-modal__toggle"><i class="icon-angle"></i></span></a>
                    <ul class="nav-modal__submenu">
                        <li class="nav-modal__subitem"><a href="/perevozka-gruzov" class="nav-modal__sublink">Перевозка грузов</a></li>
                        <li class="nav-modal__subitem"><a href="/ofisnyy-pereezd" class="nav-modal__sublink">Офисный переезд</a></li>
                        <li class="nav-modal__subitem"><a href="/pogruzochno-razgruzochnye-raboty" class="nav-modal__sublink">Погрузочно-разгрузочные работы</a></li>
                        <li class="nav-modal__subitem"><a href="/avtokonsolidaciya" class="nav-modal__sublink">Автоконсолидация</a></li>
                        <li class="nav-modal__subitem"><a href="/upakovka-gruzov" class="nav-modal__sublink">Упаковка грузов</a></li>
                    </ul>
                </li>
                <li class="nav-modal__item"><a href="/o-kompanii" class="nav-modal__link">О компании</a></li>
                <li class="nav-modal__item"><a href="/otzyvy" class="nav-modal__link">Отзывы</a></li>
                <li class="nav-modal__item"><a href="/nayti-posylku" class="nav-modal__link">Найти посылку</a></li>
                <li class="nav-modal__item"><a href="/news" class="nav-modal__link">Новости</a></li>
                <li class="nav-modal__item"><a href="/kontakty" class="nav-modal__link">Контакты</a></li>
            </ul>
            <div style="margin-left: 20px; margin-top:20px;">
                <a href="/cabinet/auth" class="btn btn_login" style="background-color: #D0171C;">Вход</a>
                <a href="/cabinet/registration" class="btn btn_register" style="background-color: #D0171C;">Регистрация</a>
            </div>
        </nav>
    </div>
</div>

<div class="wrapper">
    <header class="header">
        <div class="header-top">
            <div class="container">
                <div class="header-top__wrapper">
                    <div class="header-top__logo">
                        <a href="/" class="logo">
                            <img src="/images/new-logo.png" alt="Logexim Express" class="logo__icon">
                        </a>
                    </div>
                    <div class="header-top__phones">
                        <a href="tel:+77072301565"><i class="icon-mobile-phone"></i>+7 (707) 230 15 65</a>
                        <a href="tel:+77273209669"><i class="icon-old-phone"></i>+7 (727) 320 96 69</a>
                    </div>
                    <div class="header-top__action">
                        <a href="#" class="btn btn_anim" data-fancybox data-src="#modalRecall">
                            <span class="btn__lg">Заказать звонок</span>
                            <span class="btn__mb"><i class="icon-mobile-phone"></i></span>
                        </a>
                        <a class="header-top__burger btn-burger" href="#">
                            <div class="btn-burger__icon">
                                <div class="btn-burger__line btn-burger__line_1"></div>
                                <div class="btn-burger__line btn-burger__line_2"></div>
                                <div class="btn-burger__line btn-burger__line_3"></div>
                                <div class="btn-burger__line btn-burger__line_4"></div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <style>
            .header-buttons { display: flex; align-items: center; margin-left: auto; }
            .btn_login, .btn_register { margin-right: 15px; padding: 10px 20px; text-decoration: none; color: #fff; background-color: #ff0000; border-radius: 5px; transition: background-color 0.3s; }
            .btn_login:hover, .btn_register:hover { background-color: #cc0000; }
            @media (max-width: 768px) { .header-buttons { display: none; } .nav-main__item_mobile { display: block; } }
            .nav-main__item_mobile { display: none; }
            ._sticky-menu .header-bottom .container .header-bottom__wrapper .header-buttons .btn_login,
            ._sticky-menu .header-bottom .container .header-bottom__wrapper .header-buttons .btn_register { display: none; }
        </style>
        <div class="header-bottom">
            <div class="container">
                <div class="header-bottom__wrapper">
                    <a href="/" class="logo logo_sticky">
                        <img src="/images/new-logo.png" alt="Logexim Express" class="logo__icon">
                    </a>
                    <nav class="nav-main">
                        <ul class="nav-main__menu">
                            <li class="nav-main__item">
                                <a href="#" class="nav-main__link">Услуги</a>
                                <span class="nav-main__toggle"><i class="icon-angle"></i></span>
                                <ul class="nav-main__submenu">
                                    <li class="nav-main__subitem"><a href="/perevozka-gruzov" class="nav-main__sublink">Перевозка грузов</a></li>
                                    <li class="nav-main__subitem"><a href="/ofisnyy-pereezd" class="nav-main__sublink">Офисный переезд</a></li>
                                    <li class="nav-main__subitem"><a href="/pogruzochno-razgruzochnye-raboty" class="nav-main__sublink">Погрузочно-разгрузочные работы</a></li>
                                    <li class="nav-main__subitem"><a href="/avtokonsolidaciya" class="nav-main__sublink">Автоконсолидация</a></li>
                                    <li class="nav-main__subitem"><a href="/upakovka-gruzov" class="nav-main__sublink">Упаковка грузов</a></li>
                                </ul>
                            </li>
                            <li class="nav-main__item"><a href="/o-kompanii" class="nav-main__link">О компании</a></li>
                            <li class="nav-main__item"><a href="/otzyvy" class="nav-main__link">Отзывы</a></li>
                            <li class="nav-main__item"><a href="/nayti-posylku" class="nav-main__link">Найти посылку</a></li>
                            <li class="nav-main__item"><a href="/news" class="nav-main__link">Новости</a></li>
                            <li class="nav-main__item"><a href="/kontakty" class="nav-main__link">Контакты</a></li>
                            <li class="nav-main__item nav-main__item_mobile"><a href="/cabinet/auth" class="nav-main__link">Вход</a></li>
                            <li class="nav-main__item nav-main__item_mobile"><a href="/cabinet/registration" class="nav-main__link">Регистрация</a></li>
                        </ul>
                    </nav>
                    <div class="header-buttons">
                        <a href="/cabinet/auth" class="btn btn_login" style="background-color: #D0171C;">Вход</a>
                        <a href="/cabinet/registration" class="btn btn_register" style="background-color: #D0171C;">Регистрация</a>
                        <a href="#" class="btn btn_anim call_sticky" data-fancybox data-src="#modalRecall">
                            <span class="btn__lg">Заказать звонок</span>
                            <span class="btn__mb"><i class="icon-mobile-phone"></i></span>
                        </a>
                    </div>
                    <a class="btn-burger btn-burger_sticky" href="#">
                        <div class="btn-burger__icon">
                            <div class="btn-burger__line btn-burger__line_1"></div>
                            <div class="btn-burger__line btn-burger__line_2"></div>
                            <div class="btn-burger__line btn-burger__line_3"></div>
                            <div class="btn-burger__line btn-burger__line_4"></div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </header>

    @yield('content')

    <footer class="footer">
        <div class="container">
            <div class="footer__main">
                <div class="footer__logo">
                    <a href="/" class="logo">
                        <img src="/images/new-logo.png" alt="Logexim Express" class="logo__icon">
                    </a>
                </div>
                <div class="footer__nav">
                    <nav class="nav-footer">
                        <ul class="nav-footer__menu">
                            <li class="nav-footer__item"><a href="/o-kompanii" class="nav-footer__link">О компании</a></li>
                            <li class="nav-footer__item">
                                <a href="#" class="nav-footer__link">Услуги</a>
                                <span class="nav-footer__toggle"><i class="icon-angle"></i></span>
                                <ul class="nav-footer__submenu">
                                    <li class="nav-footer__subitem"><a href="/perevozka-gruzov" class="nav-footer__sublink">Перевозка грузов</a></li>
                                    <li class="nav-footer__subitem"><a href="/ofisnyy-pereezd" class="nav-footer__sublink">Офисный переезд</a></li>
                                    <li class="nav-footer__subitem"><a href="/pogruzochno-razgruzochnye-raboty" class="nav-footer__sublink">Погрузочно-разгрузочные работы</a></li>
                                    <li class="nav-footer__subitem"><a href="/avtokonsolidaciya" class="nav-footer__sublink">Автоконсолидация</a></li>
                                    <li class="nav-footer__subitem"><a href="/upakovka-gruzov" class="nav-footer__sublink">Упаковка грузов</a></li>
                                </ul>
                            </li>
                            <li class="nav-footer__item"><a href="/news" class="nav-footer__link">Новости</a></li>
                            <li class="nav-footer__item"><a href="/nayti-posylku" class="nav-footer__link">Найти посылку</a></li>
                        </ul>
                    </nav>
                </div>
                <div class="footer__social">
                    <a href="https://www.instagram.com/rkc_logexim/" target="_blank"><i class="icon-instagram"></i></a>
                </div>
            </div>
            <div class="footer__contacts">
                <ul class="footer__msngr">
                    <li><a href="#"><i class="icon-telegram"></i>Telegram</a></li>
                    <li><a href="https://wa.me/77072301565?text=%D0%94%D0%BE%D0%B1%D1%80%D1%8B%D0%B9%20%D0%B4%D0%B5%D0%BD%D1%8C%2C%20%D0%B8%D0%BD%D1%82%D0%B5%D1%80%D0%B5%D1%81%D1%83%D0%B5%D1%82%20%D0%BA%D0%BE%D0%BD%D1%81%D1%83%D0%BB%D1%8C%D1%82%D0%B0%D1%86%D0%B8%D1%8F"><i class="icon-whatsapp"></i>WhatsApp</a></li>
                    <li><a href="/chasto-zadavaemye-voprosy"><i class="icon-info"></i>Частые вопросы</a></li>
                    <li><a href="mailto:sales@logeximexpress.kz"><i class="icon-email"></i>sales@logeximexpress.kz</a></li>
                </ul>
                <div class="footer__content">
                    <div class="footer__address">
                        <h4>Адрес</h4>
                        <p>Республика Казахстан, Алматы, Ул. Коммунальная 3А</p>
                    </div>
                    <div class="footer__address">
                        <h4>Адрес склада</h4>
                        <p>трасса Алматы-Усть-Каменогорск, ул, Аксуат 110</p>
                    </div>
                </div>
            </div>
            <div class="footer__bottom">
                <div class="footer__copyright">Copyright &copy; {{ date('Y') }} <a href="https://mdlab.kz/" target="_blank">&laquo;Modern Design Lab&raquo;</a>. Все права защищены.</div>
                <div class="footer__docs">
                    <a href="/upload/dogovor.pdf">Скачать стандартный договор LogExim Express</a>
                    <a href="/upload/avia.pdf">Авиа тариф КЗ</a>
                    <a href="/upload/astana.pdf">Астана - тарифы-общие</a>
                    <a href="/upload/kp.pdf">КП Экспресс</a>
                </div>
            </div>
        </div>
    </footer>
</div>

<style>
    .footer__docs { display: flex; flex-wrap: wrap; gap: 5px; justify-content: flex-start; align-items: flex-start; }
    .footer__docs a { display: inline-block; margin: 0; font-size: 16px; color: #1D1D1D; font-weight: 500; border-bottom: 1px solid rgba(208, 23, 28, 0.2); padding: 4px 0; position: relative; z-index: 1; transition: all 250ms; margin-right: 34px; white-space: nowrap; }
    .footer__docs a::before { content: ""; display: block; position: absolute; bottom: 0; left: 0; right: 0; height: 0; z-index: -1; background: rgba(208, 23, 28, 0.1); transition: height 250ms; border-radius: 2px; }
    .footer__docs a:hover { border-color: rgba(208, 23, 28, 0.1); }
    .footer__docs a:hover:before { height: 100%; }
    @media (max-width: 1280px) { .footer__docs a { font-size: 14px; } }
    .footer__contacts { display: grid; grid-template-columns: 35% 1fr; gap: 40px; padding: 40px 0; border-bottom: 1px solid #e9e9e9; }
    @media (max-width: 1100px) { .footer__contacts { padding: 30px 0; grid-template-columns: 1fr auto; } }
    @media (max-width: 680px) { .footer__contacts { padding: 20px 0; grid-template-columns: 1fr; gap: 20px; } }
    .footer__content { display: flex; gap: 10px 40px; flex-wrap: wrap; }
    .footer__content > * { flex: 1 1 calc(50% - 40px); }
    @media (max-width: 680px) { .footer__content > * { flex: 1 1 100%; } }
    .footer__bottom { padding: 40px 0; display: flex; justify-content: space-between; }
</style>

<!-- Модальные окна -->
<div class="modal" id="modalReview">
    <div class="modal__wrapper">
        <div class="modal__title">Отзыв от: <span></span></div>
        <div class="modal__text"></div>
    </div>
</div>

<div class="modal" id="modalRecall">
    <div class="modal__wrapper">
        <div class="modal__title">Оставьте Ваш номер телефона и мы перезвоним</div>
        <form class="modal__form form" id="recallForm">
            <div class="form__row">
                <label class="form-text form_col-6">
                    <span class="form-text__desc form-text__desc_top">Номер телефона:</span>
                    <input type="tel" name="recall_num" data-mask="phone" placeholder="+7 (___) __ - __ - ___" required>
                </label>
                <button type="submit" class="btn form__submit form_col-4">Отправить</button>
            </div>
        </form>
        <div id="successMessage" style="display:none; color:red; font-weight: bold;">Спасибо, мы свяжемся с вами в ближайшее время</div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
    $(document).ready(function() {
        $('#recallForm').on('submit', function(event) {
            event.preventDefault();
            var phoneNumber = $('input[name="recall_num"]').val();
            $.ajax({
                url: '/ajax/send_from',
                type: 'POST',
                data: { phone: phoneNumber },
                success: function(response) { $('#successMessage').show(); },
                error: function(xhr, status, error) { console.error('Ошибка при отправке данных: ', status, error); }
            });
        });
    });
</script>
<script src="{{ asset('assets/libs/jquery-3.3.1.min.js') }}"></script>
<script src="{{ asset('assets/libs/swiper/swiper-bundle.min.js') }}"></script>
<script src="{{ asset('assets/libs/fancybox/fancybox.umd.js') }}"></script>
<script src="{{ asset('assets/libs/jquery.maskedinput.min.js') }}"></script>
<script src="{{ asset('assets/js/main.js') }}?t={{ time() }}"></script>
@stack('scripts')
</body>
</html>
