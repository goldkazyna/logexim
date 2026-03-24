<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="/css/invoice.css">
    <title>Накладная № {{ $invoice->invoice_number }}</title>
    <style>
        .header-new { display: flex; align-items: flex-start; margin-bottom: 5px; }
        .header-col { flex: 1; }
        .header-col-left { font-size: 9px; line-height: 1.3; }
        .header-col-left p { margin: 0; }
        .header-col-left .company-name { font-weight: bold; font-size: 10px; }
        .header-col-center { text-align: center; flex: 0 0 auto; padding: 0 10px; }
        .header-col-center img { height: 50px; }
        .header-col-right { text-align: right; }
        .header-col-right .invoice-number { font-size: 18px; font-weight: bold; margin-bottom: 5px; }
        .barcode-img { margin-top: 3px; }
    </style>
</head>

<body onload="window.print()">

<div class="wrapper">

    <div class="invoice">
        <div class="header-new">
            <div class="header-col header-col-left">
                <p class="company-name">ТОО «LogExim Express»</p>
                <p>БИН 211040031302</p>
                <p>Офис: г.Алматы, ул.Нурмакова 1/1, оф. 407</p>
                <p>Склад: трасса Алматы-Усть-Каменогорск, ул. Аксуат 110</p>
                <p>Тел: +7 727 320 96 69 | +7 707 230 15 65</p>
                <p>E-mail: sale@logexim.kz</p>
            </div>
            <div class="header-col-center">
                <img src="/css/logo.png" alt="Logo">
            </div>
            <div class="header-col header-col-right">
                <div class="invoice-number">НАКЛАДНАЯ № {{ $invoice->invoice_number }}</div>
                <img class="barcode-img" src="https://barcodeapi.org/api/128/{{ $invoice->invoice_number }}" alt="{{ $invoice->invoice_number }}" style="height:50px;width:200px">
            </div>
        </div>
        <div class="main">

            <div class="main__col">
                <div class="main__header">
                    <div class="col">
                        <span>Дата</span>
                        <p>{{ \Carbon\Carbon::parse($invoice->date)->format('d.m.Y') }}</p>
                    </div>
                    <div class="col">
                        <span>Время</span>
                        <p></p>
                    </div>
                    <div class="col">
                        <span>ORG</span>
                        <p>{{ $invoice->sender_city }}</p>
                    </div>
                    <div class="col">
                        <span>DEST</span>
                        <p>{{ $invoice->recipient_city }}</p>
                    </div>
                </div>

                <div class="section">
                    <div class="section__header">
                        <h2>Отправитель</h2>
                    </div>
                    <div class="section__body">
                        <div class="row">
                            <div class="col">
                                <span>ФИО отправителя</span>
                                <p>{{ $invoice->sender_name }}</p>
                            </div>
                            <div class="col">
                                <span>Телефон</span>
                                <p>{{ $invoice->sender_phone }}</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <span>Компания</span>
                                <p>{{ $invoice->sender_company }}</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <span>Город</span>
                                <p>{{ $invoice->sender_city }}</p>
                            </div>
                            <div class="col">
                                <span>Страна</span>
                                <p>{{ $invoice->sender_country }}</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <span>Область</span>
                                <p>{{ $invoice->sender_region }}</p>
                            </div>
                            <div class="col">
                                <span>Район</span>
                                <p>{{ $invoice->sender_district }}</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <span>Адрес</span>
                                <p>{{ $invoice->sender_address }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="section__footer">
                        <div class="col">
                            <span>Примечание отправителя</span>
                        </div>
                        <div class="col">
                            <p></p>
                        </div>
                    </div>
                </div>

                <div class="section">
                    <div class="section__header">
                        <h2>Получатель</h2>
                    </div>
                    <div class="section__body">
                        <div class="row">
                            <div class="col">
                                <span>ФИО получателя</span>
                                <p>{{ $invoice->recipient_name }}</p>
                            </div>
                            <div class="col">
                                <span>Телефон</span>
                                <p>{{ $invoice->recipient_phone }}</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <span>Компания</span>
                                <p>{{ $invoice->recipient_company }}</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <span>Город</span>
                                <p>{{ $invoice->recipient_city }}</p>
                            </div>
                            <div class="col">
                                <span>Страна</span>
                                <p>{{ $invoice->recipient_country }}</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <span>Область</span>
                                <p>{{ $invoice->recipient_region }}</p>
                            </div>
                            <div class="col">
                                <span>Район</span>
                                <p>{{ $invoice->recipient_district }}</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <span>Адрес</span>
                                <p>{{ $invoice->recipient_address }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="section">
                    <div class="section__header">
                        <h2>Описание отправления</h2>
                    </div>
                    <div class="section__body">
                        <div class="row row_4-1">
                            <div class="col">
                                <span>Описание вложения</span>
                                <p>{{ $invoice->description }}</p>
                            </div>
                            <div class="col">
                                <span>Хрупкий груз</span>
                                <p class="check {{ $invoice->fragile ? '_active' : '' }}"></p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <span>Количество мест</span>
                                <p>{{ $invoice->quantity }}</p>
                            </div>
                            <div class="col">
                                <span>Вес (кг)</span>
                                <p>{{ $invoice->weight }}</p>
                            </div>
                            <div class="col">
                                <span>Объем (м³)</span>
                                <p>{{ $invoice->volume_weight }}</p>
                            </div>
                        </div>
                        <div class="row"><div class="col"><p></p></div></div>
                        <div class="row"><div class="col"><p></p></div></div>
                        <div class="row"><div class="col"><p></p></div></div>
                    </div>
                    <div class="section__footer">
                        <div class="col"><span>Особые инструкции</span></div>
                        <div class="col"><p>{{ $invoice->special }}</p></div>
                    </div>
                </div>
            </div>

            <div class="main__col">
                <div class="section">
                    <div class="section__header">
                        <h2>Информация об оплате</h2>
                    </div>
                    <div class="section__body">
                        <div class="row row_1-3 row_double">
                            <div class="col"><span>Объявленная ценность</span></div>
                            <div class="col"><p>{{ number_format($invoice->declared_value, 2) }} денег</p></div>
                        </div>
                        <div class="row row_1-3 row_double">
                            <div class="col"><span>Оплата</span></div>
                            <div class="col">
                                @if($invoice->payment != 0)
                                <p>{{ number_format($invoice->payment, 2) }}</p>
                                @endif
                            </div>
                        </div>
                        <div class="row row_double">
                            <div class="col col_nobor col_cntr">
                                <span>Отправители</span>
                                <p class="check {{ $invoice->payment_sender ? '_active' : '' }}"></p>
                            </div>
                            <div class="col col_nobor col_cntr">
                                <span>Получатели</span>
                                <p class="check {{ $invoice->payment_recipient ? '_active' : '' }}"></p>
                            </div>
                            <div class="col col_nobor col_cntr">
                                <span>По договору</span>
                                <p class="check {{ $invoice->payment_contract ? '_active' : '' }}"></p>
                            </div>
                        </div>
                        <div class="row row_double">
                            <div class="col col_nobor col_cntr">
                                <span>По счёту</span>
                                <p class="check {{ $invoice->payment_invoice ? '_active' : '' }}"></p>
                            </div>
                            <div class="col col_cntr">
                                <span>Наличными</span>
                                <p class="check {{ $invoice->payment_cash ? '_active' : '' }}"></p>
                            </div>
                            <div class="col"><p></p></div>
                        </div>
                    </div>
                </div>
                <div class="section">
                    <div class="section__header">
                        <h2>Прием отправления</h2>
                    </div>
                    <div class="section__body">
                        <div class="row row_triple">
                            <div class="col col_top">
                                <span>Я подтверждаю, что отправление не содержит предметы, запрещённые к пересылке. Обязуюсь соблюдать все правила и условия, изложенные в Руководстве по услугам исполнителя.</span>
                            </div>
                            <div class="col col_top col_checkm">
                                <span>Подпись отправителя</span>
                            </div>
                        </div>
                        <div class="row row_auto">
                            <div class="col">
                                <span>Соглашение между Исполнителем и Заказчиком о приёмке Груза на условиях, изложенных в Руководстве, считается заключённым с момента подписания Отправителем (Заказчиком) бланка накладной.</span>
                            </div>
                        </div>
                        <div class="row row_triple">
                            <div class="col col_top">
                                <span>Примечание экспедиторов</span>
                                <p></p>
                            </div>
                            <div class="col col_wrapper">
                                <div class="row-inn">
                                    <div class="col-inn col-inn_nobor">
                                        <span>Переадресация</span>
                                        <p class="check"></p>
                                    </div>
                                    <div class="col-inn">
                                        <span>Возврат</span>
                                        <p class="check"></p>
                                    </div>
                                </div>
                                <div class="row-inn">
                                    <div class="col-inn col-inn_top">
                                        <span>Подпись сотрудника</span>
                                    </div>
                                    <div class="col-inn col-inn_top">
                                        <span>№</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="section">
                    <div class="section__header">
                        <h2>Информация о получении</h2>
                    </div>
                    <div class="section__body">
                        <div class="row row_auto">
                            <div class="col">
                                <span>Я подтверждаю, что отправление доставленно и вручено в закрытом виде, без внешних повреждений упаковки, упаковочных материалов и пломб, вес отправления соответствует весу, определённому при его приёмке.</span>
                            </div>
                        </div>
                        <div class="row row_1-2 row_auto">
                            <div class="col col_top col_checkm">
                                <span>Подпись получателя</span>
                            </div>
                            <div class="col col_wrapper">
                                <div class="row-inn">
                                    <div class="col-inn col-inn_top">
                                        <span>ФИО получателя</span>
                                        <p></p>
                                    </div>
                                </div>
                                <div class="row-inn">
                                    <div class="col-inn col-inn_top">
                                        <span>Дата</span>
                                        <p></p>
                                    </div>
                                    <div class="col-inn col-inn_top">
                                        <span>Время</span>
                                        <p><br /></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>

</body>
</html>
