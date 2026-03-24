<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Личный кабинет | Logexim Express</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Личный кабинет | Logexim Express" name="description">
    <meta content="Личный кабинет | Logexim Express" name="author">

    <!-- App favicon -->
    <link rel="shortcut icon" href="/favicon.ico">

    <!-- Icons css  (Mandatory in All Pages) -->
    <link href="/cab/assets/css/icons.min.css" rel="stylesheet" type="text/css">

    <!-- Google Font Family (Mandatory in All Pages) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap" rel="stylesheet">

    <!-- App css  (Mandatory in All Pages) -->
    <link href="/cab/assets/css/app.min.css" rel="stylesheet" type="text/css">
</head>

<body>

    <div class="flex wrapper">

        <!-- Start Sidebar -->
        <aside id="app-menu"
            class="hs-overlay fixed inset-y-0 start-0 z-[60] hidden w-64 -translate-x-full transform overflow-y-auto border-e border-default-200 bg-white transition-all duration-300 hs-overlay-open:translate-x-0 lg:bottom-0 lg:end-auto lg:z-30 lg:block lg:translate-x-0 rtl:translate-x-full rtl:hs-overlay-open:translate-x-0 rtl:lg:translate-x-0 print:hidden [--body-scroll:true] [--overlay-backdrop:true] lg:[--overlay-backdrop:false]">
            <div class="sticky top-0 flex h-18 items-center justify-center px-6 logo_1">
                
                  <a href="/" class="logo">
                            <img src="/assets/img/logo.svg" alt="Logexim Express" class="logo__icon">
                            <span class="logo__name">
                                <span class="logo__top">LOGEXIM EXPRESS</span>
                                <br />
                                <span class="logo__bottom">Личный кабинет</span>
                            </span>
                        </a>
                
            </div>

            <div class="hs-accordion-group h-[calc(100%-72px)] p-4 ps-0" data-simplebar>
                <ul class="admin-menu flex w-full flex-col gap-1.5">
                   

                    <li class="menu-item">
                        <a href="/cabinet/recipient_templates"
                            class="group flex items-center gap-x-3.5 rounded-e-full px-4 py-2 text-sm font-medium text-default-700 transition-all hover:bg-default-100 hs-accordion-active:bg-default-100">
                            <i
                                class="material-symbols-rounded font-light text-2xl transition-all group-hover:fill-1">article</i>
                            <span class="menu-text"> Шаблоны отправителя </span>
                        </a>
                    </li>

                    

                    

                   <li class="menu-item">
                        <a href="/cabinet/invoices"
                            class="group flex items-center gap-x-3.5 rounded-e-full px-4 py-2 text-sm font-medium text-default-700 transition-all hover:bg-default-100">
                            <i
                                class="material-symbols-rounded font-light text-2xl transition-all group-hover:fill-1">send</i>
                            <span class="menu-text"> Накладные </span>
                        </a>
                    </li>

                    <li class="menu-item">
                        <a href="/cabinet/reports"
                            class="group flex items-center gap-x-3.5 rounded-e-full px-4 py-2 text-sm font-medium text-default-700 transition-all hover:bg-default-100">
                            <i class="material-symbols-rounded font-light text-2xl transition-all group-hover:fill-1">description</i>
                            <span class="menu-text"> Отчеты </span>
                        </a>
                    </li>

                    <li class="menu-item">
                        <a href="/cabinet/profile_settings"
                            class="group flex items-center gap-x-3.5 rounded-e-full px-4 py-2 text-sm font-medium text-default-700 transition-all hover:bg-default-100 hs-accordion-active:bg-default-100">
                            <i
                                class="material-symbols-rounded font-light text-2xl transition-all group-hover:fill-1">settings</i>
                            <span class="menu-text"> Настройка профиля </span>
                        </a>
                    </li>

                    <li class="menu-item">
                        <a href="charts-apex.html"
                            class="group flex items-center gap-x-3.5 rounded-e-full px-4 py-2 text-sm font-medium text-default-700 transition-all hover:bg-default-100 hs-accordion-active:bg-default-100">
                            <i
                                class="material-symbols-rounded font-light text-2xl transition-all group-hover:fill-1">close</i>
                            <span class="menu-text"> Выход </span>
                        </a>
                    </li>

                    

                    

                    
                </ul>
            </div>
        </aside>
        <!-- End Sidebar -->

        <!-- Start Page Content here -->
        <div class="page-content">

            <!-- Topbar Start -->
            <header class="sticky top-0 bg-white h-16 flex items-center px-5 gap-4 z-50">
                <!-- Topbar Brand Logo -->
                <a href="index.html" class="md:hidden flex">
                    <img src="assets/images/logo-sm.png" class="h-6" alt="Small logo">
                </a>

                <!-- Sidenav Menu Toggle Button -->
                <button id="button-toggle-menu" class="text-gray-500 hover:text-gray-600 p-2 rounded-full cursor-pointer"
                    data-hs-overlay="#app-menu" aria-label="Toggle navigation">
                    <i class="ti ti-menu-2 text-2xl"></i>
                </button>



<?$bin = $this->session->userdata('bin');?>
                <!-- Profile Dropdown Button -->
                <div class="relative">
                    <div class="hs-dropdown relative inline-flex [--placement:bottom-right]">
                        <button type="button" class="hs-dropdown-toggle nav-link flex items-center gap-2">
                            
                            <span class="md:flex items-center hidden">
                                <span class="font-semibold text-base"><?echo $bin?></span>
                                <i class="ti ti-chevron-down text-sm ms-2"></i>
                            </span>
                        </button>
                  
                    </div>
                </div>
            </header>
            <!-- Topbar End -->