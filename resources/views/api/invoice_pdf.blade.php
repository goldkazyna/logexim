<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 9px; }
.invoice { padding: 15px; }
.header { display: table; width: 100%; margin-bottom: 8px; }
.header-left, .header-center, .header-right { display: table-cell; vertical-align: top; }
.header-left { width: 45%; }
.header-center { width: 10%; text-align: center; }
.header-right { width: 45%; text-align: right; }
.invoice-number { font-size: 14px; font-weight: bold; margin-bottom: 4px; }
.company-info { font-size: 8px; line-height: 1.4; }
.company-info .bold { font-weight: bold; font-size: 9px; }

table { width: 100%; border-collapse: collapse; margin-bottom: 6px; }
th, td { border: 1px solid #000; padding: 4px 5px; text-align: left; vertical-align: top; }
th { background: #f0f0f0; font-size: 8px; text-align: center; }
td { font-size: 9px; }
.label { font-size: 7px; color: #666; display: block; margin-bottom: 1px; }
.value { font-size: 9px; font-weight: 600; }
.section-title { background: #000; color: #fff; font-size: 10px; font-weight: bold; text-align: center; padding: 4px; }
.check-box { display: inline-block; width: 12px; height: 12px; border: 1px solid #000; text-align: center; font-size: 10px; line-height: 12px; }
.check-active { font-weight: bold; }
.two-col { display: table; width: 100%; }
.two-col .col { display: table-cell; width: 50%; vertical-align: top; }
.two-col .col:first-child { padding-right: 3px; }
.two-col .col:last-child { padding-left: 3px; }
.footer-text { font-size: 7px; line-height: 1.3; color: #333; margin-top: 4px; }
.sig-line { border-bottom: 1px solid #000; height: 25px; margin-top: 5px; }
</style>
</head>
<body>
<div class="invoice">

    <!-- Header -->
    <div class="header">
        <div class="header-left">
            <div class="invoice-number">НАКЛАДНАЯ № {{ $invoice->invoice_number }}</div>
            <div class="company-info">
                <span class="bold">ТОО «LogExim Express» | E-mail: sale@logexim.kz</span><br>
                БИН 211040031302 | Тел: +7 727 320 96 69 | +7 707 230 15 65<br>
                Офис: г.Алматы, ул.Нурмакова 1/1, оф. 407<br>
                Склад: трасса Алматы-Усть-Каменогорск, ул. Аксуат 110
            </div>
        </div>
        <div class="header-center">
            {{-- Logo placeholder --}}
        </div>
        <div class="header-right"></div>
    </div>

    <!-- Date/Route -->
    <table>
        <tr>
            <th>Дата</th>
            <th>Время</th>
            <th>ORG</th>
            <th>DEST</th>
        </tr>
        <tr>
            <td style="text-align:center">{{ \Carbon\Carbon::parse($invoice->date)->format('d.m.Y') }}</td>
            <td style="text-align:center"></td>
            <td style="text-align:center; font-weight:bold">{{ $invoice->sender_city }}</td>
            <td style="text-align:center; font-weight:bold">{{ $invoice->recipient_city }}</td>
        </tr>
    </table>

    <div class="two-col">
        <div class="col">

    <!-- Отправитель -->
    <table>
        <tr><td colspan="2" class="section-title">Отправитель</td></tr>
        <tr>
            <td><span class="label">ФИО отправителя</span><span class="value">{{ $invoice->sender_name }}</span></td>
            <td><span class="label">Телефон</span><span class="value">{{ $invoice->sender_phone }}</span></td>
        </tr>
        <tr>
            <td colspan="2"><span class="label">Компания</span><span class="value">{{ $invoice->sender_company }}</span></td>
        </tr>
        <tr>
            <td><span class="label">Город</span><span class="value">{{ $invoice->sender_city }}</span></td>
            <td><span class="label">Страна</span><span class="value">{{ $invoice->sender_country }}</span></td>
        </tr>
        <tr>
            <td><span class="label">Область</span><span class="value">{{ $invoice->sender_region }}</span></td>
            <td><span class="label">Район</span><span class="value">{{ $invoice->sender_district }}</span></td>
        </tr>
        <tr>
            <td colspan="2"><span class="label">Адрес</span><span class="value">{{ $invoice->sender_address }}</span></td>
        </tr>
        <tr>
            <td colspan="2"><span class="label">Примечание отправителя</span></td>
        </tr>
    </table>

    <!-- Получатель -->
    <table>
        <tr><td colspan="2" class="section-title">Получатель</td></tr>
        <tr>
            <td><span class="label">ФИО получателя</span><span class="value">{{ $invoice->recipient_name }}</span></td>
            <td><span class="label">Телефон</span><span class="value">{{ $invoice->recipient_phone }}</span></td>
        </tr>
        <tr>
            <td colspan="2"><span class="label">Компания</span><span class="value">{{ $invoice->recipient_company }}</span></td>
        </tr>
        <tr>
            <td><span class="label">Город</span><span class="value">{{ $invoice->recipient_city }}</span></td>
            <td><span class="label">Страна</span><span class="value">{{ $invoice->recipient_country }}</span></td>
        </tr>
        <tr>
            <td><span class="label">Область</span><span class="value">{{ $invoice->recipient_region }}</span></td>
            <td><span class="label">Район</span><span class="value">{{ $invoice->recipient_district }}</span></td>
        </tr>
        <tr>
            <td colspan="2"><span class="label">Адрес</span><span class="value">{{ $invoice->recipient_address }}</span></td>
        </tr>
    </table>

    <!-- Описание отправления -->
    <table>
        <tr><td colspan="3" class="section-title">Описание отправления</td></tr>
        <tr>
            <td colspan="2"><span class="label">Описание вложения</span><span class="value">{{ $invoice->description }}</span></td>
            <td style="text-align:center"><span class="label">Хрупкий груз</span><span class="check-box {{ $invoice->fragile ? 'check-active' : '' }}">{{ $invoice->fragile ? '✔' : '' }}</span></td>
        </tr>
        <tr>
            <td><span class="label">Количество мест</span><span class="value">{{ $invoice->quantity }}</span></td>
            <td><span class="label">Вес (кг)</span><span class="value">{{ $invoice->weight }}</span></td>
            <td><span class="label">Объём (м³)</span><span class="value">{{ $invoice->volume_weight }}</span></td>
        </tr>
        <tr>
            <td colspan="3"><span class="label">Особые инструкции</span><span class="value">{{ $invoice->special }}</span></td>
        </tr>
    </table>

        </div>
        <div class="col">

    <!-- Оплата -->
    <table>
        <tr><td colspan="2" class="section-title">Информация об оплате</td></tr>
        <tr>
            <td><span class="label">Объявленная ценность</span></td>
            <td><span class="value">{{ number_format($invoice->declared_value, 2) }}</span></td>
        </tr>
        <tr>
            <td colspan="2" style="text-align:center">
                Отправители <span class="check-box {{ $invoice->payment_sender ? 'check-active' : '' }}">{{ $invoice->payment_sender ? '✔' : '' }}</span> &nbsp;
                Получатели <span class="check-box {{ $invoice->payment_recipient ? 'check-active' : '' }}">{{ $invoice->payment_recipient ? '✔' : '' }}</span> &nbsp;
                По договору <span class="check-box {{ $invoice->payment_contract ? 'check-active' : '' }}">{{ $invoice->payment_contract ? '✔' : '' }}</span>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="text-align:center">
                По счёту <span class="check-box {{ $invoice->payment_invoice ? 'check-active' : '' }}">{{ $invoice->payment_invoice ? '✔' : '' }}</span> &nbsp;
                Наличными <span class="check-box {{ $invoice->payment_cash ? 'check-active' : '' }}">{{ $invoice->payment_cash ? '✔' : '' }}</span>
            </td>
        </tr>
    </table>

    <!-- Приём отправления -->
    <table>
        <tr><td class="section-title">Приём отправления</td></tr>
        <tr>
            <td>
                <p class="footer-text">Я подтверждаю, что отправление не содержит предметы, запрещённые к пересылке. Обязуюсь соблюдать все правила и условия, изложенные в Руководстве по услугам исполнителя.</p>
                <div style="margin-top:8px"><span class="label">Подпись отправителя</span><div class="sig-line"></div></div>
            </td>
        </tr>
        <tr>
            <td>
                <p class="footer-text">Соглашение между Исполнителем и Заказчиком о приёмке Груза на условиях, изложенных в Руководстве, считается заключённым с момента подписания Отправителем (Заказчиком) бланка накладной.</p>
            </td>
        </tr>
        <tr>
            <td>
                <span class="label">Примечание экспедиторов</span>
                <div class="sig-line"></div>
                <div style="margin-top:5px"><span class="label">Подпись сотрудника</span><div class="sig-line"></div></div>
            </td>
        </tr>
    </table>

    <!-- Получение -->
    <table>
        <tr><td class="section-title">Информация о получении</td></tr>
        <tr>
            <td>
                <p class="footer-text">Я подтверждаю, что отправление доставлено и вручено в закрытом виде, без внешних повреждений упаковки, упаковочных материалов и пломб, вес отправления соответствует весу, определённому при его приёмке.</p>
            </td>
        </tr>
        <tr>
            <td>
                <span class="label">Подпись получателя</span><div class="sig-line"></div>
                <div style="display:table;width:100%;margin-top:5px">
                    <div style="display:table-cell;width:50%"><span class="label">ФИО получателя</span><div class="sig-line"></div></div>
                    <div style="display:table-cell;width:25%;padding-left:5px"><span class="label">Дата</span><div class="sig-line"></div></div>
                    <div style="display:table-cell;width:25%;padding-left:5px"><span class="label">Время</span><div class="sig-line"></div></div>
                </div>
            </td>
        </tr>
    </table>

        </div>
    </div>

</div>
</body>
</html>
