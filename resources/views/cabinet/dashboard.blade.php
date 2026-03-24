@extends('layouts.cabinet')
@section('title', 'Личный кабинет')
@section('content')
<main class="flex-grow p-6">
    <div class="flex items-center justify-between flex-wrap gap-2 mb-6">
        <div>
            <div class="card overflow-hidden">
                <div class="card-header"><h4 class="card-title">Личный кабинет</h4></div>
                <div><div class="overflow-x-auto"><div class="min-w-full inline-block align-middle"><div class="overflow-hidden" style="padding: 10px;">
                    <p>Добро пожаловать в личный кабинет! Слева вы видите меню, которое вы можете использовать для работы с системой:
                    <br><br>
                    <b>Накладные</b> — добавляйте накладные, которые сразу попадают в логистическую компанию и обрабатываются в реальном времени.<br>
                    <b>Шаблон отправителя</b> — заполните данные отправителя один раз, и в дальнейшем сможете выбирать его при создании накладной.<br>
                    <b>Отчеты</b> — генерируйте отчеты о созданных накладных с указанием даты и другой необходимой информации.<br>
                    <b>Настройка профиля</b> — заполните информацию о себе, чтобы данные автоматически подставлялись при создании накладных.</p>
                </div></div></div></div>
            </div>
        </div>
    </div>
</main>
@endsection
