<?php $this->load->view('templates/cabinet/block/header_view'); ?>
<style>
.common_btn {
    background-color: #D0171C; 
    border-color: #D0171C; 
    border-radius: 10px; 
    color:#ffffff;
}
.common_btn:hover {
    background-color: #a21216;
}
h2 {
    background-color: #D0171C; 
    color: #ffffff !important; 
    text-align: center; 
    border-radius: 10px;
    padding:5px;
    margin-bottom:10px;
}
.invoice-data {
    font-size: 16px;
    font-weight: bold;
    color: #333;
    margin-bottom: 20px;
}
.invoice-data span {
    display: inline-block;
    width: 200px;
    color: #555;
    font-weight: normal;
}
</style>

<main class="flex-grow p-6">
    <div class="flex items-center justify-between flex-wrap gap-2 mb-6">
        <div>
            <h4 class="text-slate-900 text-lg font-medium mb-2">Просмотр накладной № <?php echo $invoice['invoice_number']; ?></h4>

            <div class="md:flex hidden items-center gap-3 text-sm font-semibold">
                <p>Полная информация о накладной</p>
                <br />
            </div>

            <div class="mt-4">
                <!-- Дата создания -->
                <div class="invoice-data">
                    <span>Дата:</span> <?php echo date('d.m.Y', strtotime($invoice['date'])); ?>
                </div>

                <!-- Отправитель -->
                <h2>Отправитель</h2>
                <div class="invoice-data">
                    <span>ФИО отправителя:</span> <?php echo $invoice['sender_name']; ?>
                </div>
                <div class="invoice-data">
                    <span>Компания:</span> <?php echo $invoice['sender_company']; ?>
                </div>
                <div class="invoice-data">
                    <span>Телефон:</span> <?php echo $invoice['sender_phone']; ?>
                </div>
                <div class="invoice-data">
                    <span>Адрес:</span> <?php echo $invoice['sender_address'] . ', ' . $invoice['sender_city'] . ', ' . $invoice['sender_region'] . ', ' . $invoice['sender_country']; ?>
                </div>

                <!-- Получатель -->
                <h2>Получатель</h2>
                <div class="invoice-data">
                    <span>ФИО получателя:</span> <?php echo $invoice['recipient_name']; ?>
                </div>
                <div class="invoice-data">
                    <span>Компания:</span> <?php echo $invoice['recipient_company']; ?>
                </div>
                <div class="invoice-data">
                    <span>Телефон:</span> <?php echo $invoice['recipient_phone']; ?>
                </div>
                <div class="invoice-data">
                    <span>Адрес:</span> <?php echo $invoice['recipient_address'] . ', ' . $invoice['recipient_city'] . ', ' . $invoice['recipient_region'] . ', ' . $invoice['recipient_country']; ?>
                </div>

                <!-- Описание отправления -->
                <h2>Описание отправления</h2>
                <div class="invoice-data">
                    <span>Описание вложения:</span> <?php echo $invoice['description']; ?>
                </div>
                <div class="invoice-data">
                    <span>Количество мест:</span> <?php echo $invoice['quantity']; ?>
                </div>
                <div class="invoice-data">
                    <span>Вес (кг):</span> <?php echo $invoice['weight']; ?>
                </div>
                <div class="invoice-data">
                    <span>Объёмный вес (кг):</span> <?php echo $invoice['volume_weight']; ?>
                </div>
                <div class="invoice-data">
                    <span>Хрупкий груз:</span> <?php echo $invoice['fragile'] ? 'Да' : 'Нет'; ?>
                </div>

                <!-- Информация об оплате -->
                <h2>Информация об оплате</h2>
                <div class="invoice-data">
                    <span>Объявленная ценность (в KZT):</span> <?php echo $invoice['declared_value']; ?>
                </div>
                <div class="invoice-data">
                    <span>Сумма оплаты (в KZT):</span> <?php echo $invoice['payment']; ?>
                </div>
                <div class="invoice-data">
                    <span>Способ оплаты:</span>
                    <?php
                        $payment_methods = [];
                        if ($invoice['payment_sender']) $payment_methods[] = 'Оплата отправителем';
                        if ($invoice['payment_recipient']) $payment_methods[] = 'Оплата получателем';
                        if ($invoice['payment_contract']) $payment_methods[] = 'Оплата по договору';
                        if ($invoice['payment_invoice']) $payment_methods[] = 'Оплата по счету';
                        if ($invoice['payment_cash']) $payment_methods[] = 'Оплата наличными';
                        echo implode(', ', $payment_methods);
                    ?>
                </div>

                <!-- Статус накладной -->
                <div class="invoice-data">
                    <span>Статус:</span>
                    <?php 
                        switch ($invoice['status']) {
                            case 0:
                                echo 'Заявка создана';
                                break;
                            case 1:
                                echo 'Принята в работу';
                                break;
                            case 2:
                                echo 'Отправлено';
                                break;
                            case 3:
                                echo 'Исполнено';
                                break;
                            default:
                                echo 'Неизвестный статус';
                                break;
                        }
                    ?>
                </div>
                <div class="invoice-data">
                    <span>Особые инструкции:</span> <?php echo $invoice['special']; ?>
                </div>
            </div>
        </div>
    </div>
</main>

<?php $this->load->view('templates/cabinet/block/footer_view'); ?>
