@extends('layouts.app')

@section('title', 'Удаление аккаунта - LogExim Express')

@push('styles')
<style>
.privacy-wrap { max-width: 900px; margin: 0 auto; padding: 40px 20px 60px; color: #222; line-height: 1.7; }
.privacy-wrap h1 { font-size: 30px; font-weight: 700; margin-bottom: 6px; }
.privacy-wrap .updated { color: #888; font-size: 14px; margin-bottom: 30px; }
.privacy-wrap h2 { font-size: 21px; font-weight: 700; margin: 30px 0 12px; }
.privacy-wrap p { font-size: 16px; margin: 0 0 14px; }
.privacy-wrap ol, .privacy-wrap ul { margin: 0 0 16px 22px; }
.privacy-wrap li { font-size: 16px; margin-bottom: 8px; }
.privacy-wrap a { color: #e2001a; }
.privacy-wrap .box { background:#f8f9fa; border-radius:12px; padding:18px 22px; margin: 0 0 18px; }
</style>
@endpush

@section('content')
<section class="main-section">
<div class="privacy-wrap">

    <h1>Удаление аккаунта и данных</h1>
    <p class="updated">Приложение: LogExim Express · ТОО «LogExim Express»</p>

    <p>Вы можете в любой момент удалить свой аккаунт в приложении «LogExim Express» и связанные
    с ним персональные данные. Ниже описаны два способа.</p>

    <h2>Способ 1. Удаление прямо в приложении</h2>
    <div class="box">
        <ol>
            <li>Откройте приложение и войдите в свой аккаунт.</li>
            <li>Перейдите на вкладку <b>«Профиль»</b>.</li>
            <li>Нажмите кнопку <b>«Удалить аккаунт»</b>.</li>
            <li>Подтвердите удаление в появившемся окне.</li>
        </ol>
        <p style="margin:0;">Аккаунт и связанные с ним персональные данные будут удалены сразу.</p>
    </div>

    <h2>Способ 2. Запрос на удаление по электронной почте</h2>
    <p>Если у вас нет доступа к приложению, отправьте запрос на удаление на адрес
    <a href="mailto:info@logeximexpress.kz">info@logeximexpress.kz</a>, указав БИН вашей компании.
    Мы обработаем запрос в течение разумного срока и подтвердим удаление.</p>

    <h2>Какие данные удаляются</h2>
    <p>При удалении аккаунта удаляются: учётная запись, наименование компании, БИН, имя руководителя,
    адрес электронной почты, номер телефона и адрес.</p>

    <h2>Какие данные могут сохраняться</h2>
    <p>Данные об уже оформленных накладных и доставках могут храниться в течение срока, установленного
    требованиями бухгалтерского и налогового учёта Республики Казахстан, в обезличенном для аккаунта виде.
    По истечении этого срока такие данные удаляются.</p>

    <h2>Контакты</h2>
    <p>
        <b>ТОО «LogExim Express»</b><br>
        Адрес: Республика Казахстан, г. Алматы, ул. Коммунальная 3А<br>
        Телефон: +7 (707) 230 15 65, +7 (727) 351 73 41<br>
        E-mail: <a href="mailto:info@logeximexpress.kz">info@logeximexpress.kz</a>
    </p>

</div>
</section>
@endsection
