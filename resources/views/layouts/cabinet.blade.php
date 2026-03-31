<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>@yield('title', 'Личный кабинет') | Logexim Express</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="/favicon.ico">
    <link href="/cab/assets/css/icons.min.css" rel="stylesheet" type="text/css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap" rel="stylesheet">
    <link href="/cab/assets/css/app.min.css" rel="stylesheet" type="text/css">
    @stack('styles')
</head>
<body>
    <div class="flex wrapper">
        <aside id="app-menu" class="hs-overlay fixed inset-y-0 start-0 z-[60] hidden w-64 -translate-x-full transform overflow-y-auto border-e border-default-200 bg-white transition-all duration-300 hs-overlay-open:translate-x-0 lg:bottom-0 lg:end-auto lg:z-30 lg:block lg:translate-x-0 rtl:translate-x-full rtl:hs-overlay-open:translate-x-0 rtl:lg:translate-x-0 print:hidden [--body-scroll:true] [--overlay-backdrop:true] lg:[--overlay-backdrop:false]">
            <div class="sticky top-0 flex h-18 items-center justify-center px-6 logo_1">
                <a href="/" class="logo">
                    <img src="/images/new-logo.png" alt="Logexim Express" class="logo__icon">
                    <span class="logo__name">
                        <span class="logo__top">LOGEXIM EXPRESS</span><br>
                        <span class="logo__bottom">Личный кабинет</span>
                    </span>
                </a>
            </div>
            <div class="hs-accordion-group h-[calc(100%-72px)] p-4 ps-0" data-simplebar>
                <ul class="admin-menu flex w-full flex-col gap-1.5">
                    <li class="menu-item">
                        <a href="/cabinet" class="group flex items-center gap-x-3.5 rounded-e-full px-4 py-2 text-sm font-medium text-default-700 transition-all hover:bg-default-100">
                            <i class="material-symbols-rounded font-light text-2xl transition-all group-hover:fill-1">home</i>
                            <span class="menu-text">Главная</span>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="/cabinet/recipient_templates" class="group flex items-center gap-x-3.5 rounded-e-full px-4 py-2 text-sm font-medium text-default-700 transition-all hover:bg-default-100">
                            <i class="material-symbols-rounded font-light text-2xl transition-all group-hover:fill-1">article</i>
                            <span class="menu-text">Шаблоны отправителя</span>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="/cabinet/description_templates" class="group flex items-center gap-x-3.5 rounded-e-full px-4 py-2 text-sm font-medium text-default-700 transition-all hover:bg-default-100">
                            <i class="material-symbols-rounded font-light text-2xl transition-all group-hover:fill-1">description</i>
                            <span class="menu-text">Шаблоны описания</span>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="/cabinet/invoices" class="group flex items-center gap-x-3.5 rounded-e-full px-4 py-2 text-sm font-medium text-default-700 transition-all hover:bg-default-100">
                            <i class="material-symbols-rounded font-light text-2xl transition-all group-hover:fill-1">send</i>
                            <span class="menu-text">Накладные</span>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="/cabinet/reports" class="group flex items-center gap-x-3.5 rounded-e-full px-4 py-2 text-sm font-medium text-default-700 transition-all hover:bg-default-100">
                            <i class="material-symbols-rounded font-light text-2xl transition-all group-hover:fill-1">description</i>
                            <span class="menu-text">Отчеты</span>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="/cabinet/profile_settings" class="group flex items-center gap-x-3.5 rounded-e-full px-4 py-2 text-sm font-medium text-default-700 transition-all hover:bg-default-100">
                            <i class="material-symbols-rounded font-light text-2xl transition-all group-hover:fill-1">settings</i>
                            <span class="menu-text">Настройка профиля</span>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="/cabinet/logout" class="group flex items-center gap-x-3.5 rounded-e-full px-4 py-2 text-sm font-medium text-default-700 transition-all hover:bg-default-100">
                            <i class="material-symbols-rounded font-light text-2xl transition-all group-hover:fill-1">close</i>
                            <span class="menu-text">Выход</span>
                        </a>
                    </li>
                </ul>
            </div>
        </aside>

        <div class="page-content">
            <header class="sticky top-0 bg-white h-16 flex items-center px-5 gap-4 z-50">
                <a href="/cabinet" class="md:hidden flex">
                    <img src="/images/new-logo.png" class="h-6" alt="Logo">
                </a>
                <button id="button-toggle-menu" class="text-gray-500 hover:text-gray-600 p-2 rounded-full cursor-pointer" data-hs-overlay="#app-menu">
                    <i class="ti ti-menu-2 text-2xl"></i>
                </button>
                <div class="relative">
                    <span class="font-semibold text-base">{{ session('bin') }}</span>
                </div>
            </header>

            @yield('content')

            <footer class="footer h-16 flex items-center px-6 bg-white border-t border-gray-200 shadow">
                <div class="flex md:justify-between justify-center w-full gap-4">
                    <div><script>document.write(new Date().getFullYear())</script> &copy; logeximexpress.kz</div>
                    <div class="md:flex hidden gap-2 item-center md:justify-end">Design &amp; Develop by <a href="https://mdlab.kz/" class="text-primary">Modern Design Lab</a></div>
                </div>
            </footer>
        </div>
    </div>

    <script src="/cab/assets/libs/jquery/jquery.min.js"></script>
    <script src="/cab/assets/libs/preline/preline.js"></script>
    <script src="/cab/assets/libs/simplebar/simplebar.min.js"></script>
    <script src="/cab/assets/libs/iconify-icon/iconify-icon.min.js"></script>
    <script src="/cab/assets/libs/node-waves/waves.min.js"></script>
    <script src="/cab/assets/js/app.js"></script>
    <script>$.ajaxSetup({headers:{'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')}});</script>
    @stack('scripts')
</body>
</html>
