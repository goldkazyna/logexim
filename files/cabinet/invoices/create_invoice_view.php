<?$this->load->view('templates/cabinet/block/header_view');?>
<style>
.common_btn{
    background-color: #D0171C; border-color: #D0171C; border-radius: 10px; color:#ffffff;
}
.common_btn:hover{
    background-color: #a21216;
}
h2 {
    background-color: #D0171C; color:#ffffff !important; text-align: center; border-radius:10px;
}

</style>

            <main class="flex-grow p-6">
    <!-- Page Title Start -->
    <div class="flex items-center justify-between flex-wrap gap-2 mb-6">
        <div>
            <h4 class="text-slate-900 text-lg font-medium mb-2">Добавление накладной</h4>

            <div class="md:flex hidden items-center gap-3 text-sm font-semibold">
                <p>Заполните данные для отправки накладной

                </p>
                <br />
            </div>
            <div class="mt-4">
                          <?php if ($this->session->flashdata('success')): ?>
                            <div id="success-alert" class="bg-primary text-sm text-white rounded-md p-4 mb-4" role="alert">
                                <?php echo $this->session->flashdata('success'); ?>
                            </div>
                            <?php endif; ?>
            </div>
            <div class="">
    <form action="<?php echo base_url('cabinet/save_invoices'); ?>" method="post" class="mt-4">
        <!-- Дата -->
        <div class="mb-4">
            <label for="date" class="text-gray-800 font-bold text-base inline-block mb-2">Дата</label>
            <input type="date" id="date" name="date" class="form-input w-full md:w-1/2" required style="cursor: pointer;">
        </div>
        
        <!-- Отправитель -->
        <h2 class="text-gray-800 font-bold text-lg mb-4">Отправитель</h2>
        <button type="button" class="btn btn-primary common_btn mb-4" id="template-button-2">Выбрать из шаблона</button>

        <div class="mb-4">
            <label for="sender-name" class="text-gray-800 font-bold text-base inline-block mb-2">ФИО отправителя</label>
            <input type="text" id="sender-name" name="sender_name" class="form-input w-full md:w-1/2" value="<?echo $user['director_name']?>" placeholder="Введите ФИО отправителя" required>
        </div>

        <div class="mb-4">
            <label for="sender-phone" class="text-gray-800 font-bold text-base inline-block mb-2">Телефон</label>
            <input type="tel" id="sender-phone" name="sender_phone" class="form-input w-full md:w-1/2" value="<?echo $user['phone']?>" placeholder="Введите телефон отправителя" required>
        </div>

        <div class="mb-4">
            <label for="sender-company" class="text-gray-800 font-bold text-base inline-block mb-2">Компания</label>
            <input type="text" id="sender-company" name="sender_company" class="form-input w-full md:w-1/2" value='<?echo $user['company_name']?>' placeholder="Введите название компании (опционально)">
        </div>

        <div class="mb-4">
            <label for="sender-city" class="text-gray-800 font-bold text-base inline-block mb-2">Город</label>
            <input type="text" id="sender-city" name="sender_city" class="form-input w-full md:w-1/2" value='<?echo $user['city']?>' placeholder="Введите город отправителя" required>
        </div>

        <div class="mb-4">
            <label for="sender-country" class="text-gray-800 font-bold text-base inline-block mb-2">Страна</label>
            <input type="text" id="sender-country" name="sender_country" class="form-input w-full md:w-1/2" value='<?echo $user['country']?>' placeholder="Введите страну отправителя" required>
        </div>

        <div class="mb-4">
            <label for="sender-region" class="text-gray-800 font-bold text-base inline-block mb-2">Область</label>
            <input type="text" id="sender-region" name="sender_region" class="form-input w-full md:w-1/2" value='<?echo $user['region']?>' placeholder="Введите область отправителя">
        </div>

        <div class="mb-4">
            <label for="sender-district" class="text-gray-800 font-bold text-base inline-block mb-2">Район</label>
            <input type="text" id="sender-district" name="sender_district" class="form-input w-full md:w-1/2" value='<?echo $user['district']?>' placeholder="Введите район отправителя">
        </div>

        <div class="mb-4">
            <label for="sender-address" class="text-gray-800 font-bold text-base inline-block mb-2">Адрес</label>
            <input type="text" id="sender-address" name="sender_address" class="form-input w-full md:w-1/2" value='<?echo $user['address']?>' placeholder="Введите адрес отправителя" required>
        </div>

        <!-- Получатель -->
        <h2 class="text-gray-800 font-bold text-lg mb-4">Получатель</h2>

         <button type="button" class="btn btn-primary common_btn mb-4" id="template-button">Выбрать из шаблона</button>

        <div class="mb-4">
            <label for="recipient-name" class="text-gray-800 font-bold text-base inline-block mb-2">ФИО получателя</label>
            <input type="text" id="recipient-name" name="recipient_name" class="form-input w-full md:w-1/2" placeholder="Введите ФИО получателя" required>
        </div>

        <div class="mb-4">
            <label for="recipient-phone" class="text-gray-800 font-bold text-base inline-block mb-2">Телефон</label>
            <input type="tel" id="recipient-phone" name="recipient_phone" class="form-input w-full md:w-1/2" placeholder="Введите телефон получателя" required>
        </div>

        <div class="mb-4">
            <label for="recipient-company" class="text-gray-800 font-bold text-base inline-block mb-2">Компания</label>
            <input type="text" id="recipient-company" name="recipient_company" class="form-input w-full md:w-1/2" placeholder="Введите название компании">
        </div>

        <div class="mb-4">
            <label for="recipient-city" class="text-gray-800 font-bold text-base inline-block mb-2">Город</label>
            <input type="text" id="recipient-city" name="recipient_city" class="form-input w-full md:w-1/2" placeholder="Введите город получателя" required>
        </div>

        <div class="mb-4">
            <label for="recipient-country" class="text-gray-800 font-bold text-base inline-block mb-2">Страна</label>
            <input type="text" id="recipient-country" name="recipient_country" class="form-input w-full md:w-1/2" placeholder="Введите страну получателя" required>
        </div>

        <div class="mb-4">
            <label for="recipient-region" class="text-gray-800 font-bold text-base inline-block mb-2">Область</label>
            <input type="text" id="recipient-region" name="recipient_region" class="form-input w-full md:w-1/2" placeholder="Введите область получателя">
        </div>

        <div class="mb-4">
            <label for="recipient-district" class="text-gray-800 font-bold text-base inline-block mb-2">Район</label>
            <input type="text" id="recipient-district" name="recipient_district" class="form-input w-full md:w-1/2" placeholder="Введите район получателя">
        </div>

        <div class="mb-4">
            <label for="recipient-address" class="text-gray-800 font-bold text-base inline-block mb-2">Адрес</label>
            <input type="text" id="recipient-address" name="recipient_address" class="form-input w-full md:w-1/2" placeholder="Введите адрес получателя" required>
        </div>

        <!-- Описание отправления -->
        <h2 class="text-gray-800 font-bold text-lg mb-4">Описание отправления</h2>
        
        <div class="mb-4">
            <label for="description" class="text-gray-800 font-bold text-base inline-block mb-2">Описание вложения</label>
            <input type="text" id="description" name="description" class="form-input w-full md:w-1/2" placeholder="Введите описание вложения" required>
        </div>

        <div class="mb-4">
            <label for="quantity" class="text-gray-800 font-bold text-base inline-block mb-2">Количество мест</label>
            <input type="number" id="quantity" name="quantity" class="form-input w-full md:w-1/2" placeholder="Введите количество мест" required>
        </div>

        <div class="mb-4">
            <label for="weight" class="text-gray-800 font-bold text-base inline-block mb-2">Вес (кг)</label>
            <input type="number" id="weight" name="weight" class="form-input w-full md:w-1/2" placeholder="Введите вес" required>
        </div>

        <div class="mb-4">
            <label for="volume-weight" class="text-gray-800 font-bold text-base inline-block mb-2">Объёмный вес (кг)</label>
            <input type="number" id="volume-weight" name="volume_weight" class="form-input w-full md:w-1/2" placeholder="Введите объёмный вес">
        </div>

        <div class="mb-4">
            <label class="text-gray-800 font-bold text-base inline-block mb-2">
                <input type="checkbox" name="fragile" class="form-checkbox h-5 w-5 text-indigo-600 rounded-md">
                Хрупкий груз
            </label>
        </div>

        <!-- Информация об оплате -->
        <h2 class="text-gray-800 font-bold text-lg mb-4">Информация об оплате</h2>
        <div class="mb-4">
            <label for="declared-value" class="text-gray-800 font-bold text-base inline-block mb-2">Объявленная ценность (в национальной валюте)</label>
            <input type="number" id="declared-value" name="declared_value" class="form-input w-full md:w-1/2" placeholder="Введите объявленную ценность" required>
        </div>
        


        <!-- Стилизованные чекбоксы -->
        <div class="mb-4 flex flex-wrap gap-4">
            <label class="flex items-center space-x-3">
                <input type="checkbox" name="payment_sender" class="form-checkbox h-5 w-5 text-[#D0171C] rounded-md">
                <span class="text-gray-800 font-medium text-base">Оплата отправителем</span>
            </label>

            <label class="flex items-center space-x-3">
                <input type="checkbox" name="payment_recipient" class="form-checkbox h-5 w-5 text-[#D0171C] rounded-md">
                <span class="text-gray-800 font-medium text-base">Оплата получателем</span>
            </label>

            <label class="flex items-center space-x-3">
                <input type="checkbox" name="payment_contract" class="form-checkbox h-5 w-5 text-[#D0171C] rounded-md">
                <span class="text-gray-800 font-medium text-base">Оплата по договору</span>
            </label>

            <label class="flex items-center space-x-3">
                <input type="checkbox" name="payment_invoice" class="form-checkbox h-5 w-5 text-[#D0171C] rounded-md">
                <span class="text-gray-800 font-medium text-base">Оплата по счету</span>
            </label>

            <label class="flex items-center space-x-3">
                <input type="checkbox" name="payment_cash" class="form-checkbox h-5 w-5 text-[#D0171C] rounded-md">
                <span class="text-gray-800 font-medium text-base">Оплата наличными</span>
            </label>
        </div>
        <div class="mb-4">
            <label for="sender-district" class="text-gray-800 font-bold text-base inline-block mb-2">Особые инструкции</label>
            <input type="text" id="sender-district" name="special" class="form-input w-full md:w-1/2" value='' placeholder="Введите особые инструкции">
        </div>
        <button type="submit" class="btn btn-primary common_btn">Отправить</button>
    </form>
</div>


        </div>
    </div>
    <!-- Page Title End -->
    <div id="template-modal" class="hidden fixed inset-0 bg-opacity-75 flex justify-center items-center">
    <div class="bg-white w-96 rounded-lg shadow-lg p-6">
        <h2 class="text-xl font-bold mb-4" style="padding: 10px;">Выберите компанию из шаблона</h2>
        <ul id="company-list" class="space-y-2">
            <?php foreach ($templates as $template): ?>
                <li>
                    <button type="button" class="select-company-btn" data-id="<?php echo $template['id']; ?>">
                        <?php echo $template['company']; ?><br />
                        <span style="font-size: 11px;"><?php echo $template['recipient_name']; ?>-<?php echo $template['city']; ?></span>
                    </button>
                </li>
            <?php endforeach; ?>
        </ul>
         <button type="button" class="close-modal-btn mt-4 bg-red-600 text-white w-full px-4 py-2 rounded">Закрыть</button>
    </div>
</div>



<div id="template-modal-2" class="hidden fixed inset-0 bg-opacity-75 flex justify-center items-center">
    <div class="bg-white w-96 rounded-lg shadow-lg p-6">
        <h2 class="text-xl font-bold mb-4" style="padding: 10px;">Выберите компанию из шаблона</h2>
        <ul id="company-list" class="space-y-2">
            <?php foreach ($templates as $template): ?>
                <li>
                    <button type="button" class="select-company-btn-2" data-id="<?php echo $template['id']; ?>">
                        <?php echo $template['company']; ?><br />
                        <span style="font-size: 11px;"><?php echo $template['recipient_name']; ?>-<?php echo $template['city']; ?></span>
                    </button>
                </li>
            <?php endforeach; ?>
        </ul>
         <button type="button" class="close-modal-btn-2 mt-4 bg-red-600 text-white w-full px-4 py-2 rounded">Закрыть</button>
    </div>
</div>
</main>





<!-- Модальное окно для выбора компании -->

<style>


#template-modal .select-company-btn {
    display: block;
    width: 100%;
    padding: 5px;
    background-color: #ffffff;
    
    color: #000000;
    border: 1px solid #cccccc;
    border-radius: 5px;
    text-align: center;
    cursor: pointer;
}

#template-modal .select-company-btn:hover {
    background-color: #a21216;
    color:#ffffff;
}

#template-modal #company-list {
    max-height: 300px; /* Ограничиваем высоту списка */
    overflow-y: auto; /* Добавляем вертикальную прокрутку */
    padding-right: 10px; /* Отступ для удобного просмотра */
}

/* Стили для скролл-бара */
#template-modal #company-list::-webkit-scrollbar {
    width: 6px; /* Ширина скроллбара */
}

#template-modal #company-list::-webkit-scrollbar-thumb {
    background-color: #a21216; /* Цвет ползунка */
    border-radius: 10px; /* Скругленные углы */
}

#template-modal #company-list::-webkit-scrollbar-track {
    background-color: #f1f1f1; /* Цвет фона скроллбара */
}

#template-modal-2 .select-company-btn-2 {
    display: block;
    width: 100%;
    padding: 5px;
    background-color: #ffffff;
    
    color: #000000;
    border: 1px solid #cccccc;
    border-radius: 5px;
    text-align: center;
    cursor: pointer;
}

#template-modal-2 .select-company-btn-2:hover {
    background-color: #a21216;
    color:#ffffff;
}


#template-modal-2 #company-list {
    max-height: 300px; /* Ограничиваем высоту списка */
    overflow-y: auto; /* Добавляем вертикальную прокрутку */
    padding-right: 10px; /* Отступ для удобного просмотра */
}

/* Стили для скролл-бара */
#template-modal-2 #company-list::-webkit-scrollbar {
    width: 6px; /* Ширина скроллбара */
}

#template-modal-2 #company-list::-webkit-scrollbar-thumb {
    background-color: #a21216; /* Цвет ползунка */
    border-radius: 10px; /* Скругленные углы */
}

#template-modal-2 #company-list::-webkit-scrollbar-track {
    background-color: #f1f1f1; /* Цвет фона скроллбара */
}


</style>
<!-- JavaScript для скрытия сообщения через 5 секунд -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(document).ready(function() {
    // Открытие модального окна
    $('#template-button').click(function() {
        $('#template-modal').removeClass('hidden');
    });
    $('#template-button-2').click(function() {
        $('#template-modal-2').removeClass('hidden');
    });
    // Получение данных компании через Ajax
    $('.select-company-btn-2').click(function() {
        var templateId = $(this).data('id');
        
        // Выполняем Ajax-запрос
        $.ajax({
            url: '<?php echo base_url('cabinet/get_recipient_template'); ?>',
            type: 'POST',
            data: {id: templateId},
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    // Заполняем поля в форме данными получателя
                    $('#sender-company').val(response.company);
                    $('#sender-name').val(response.recipient_name);
                    $('#sender-phone').val(response.recipient_phone);
                    $('#sender-address').val(response.address);
                    $('#sender-city').val(response.city);
                    $('#sender-country').val(response.country);
                    $('#sender-region').val(response.region);
                    $('#sender-district').val(response.district);
                    
                    // Закрываем модальное окно
                    $('#template-modal-2').addClass('hidden');
                } else {
                    alert('Ошибка при получении данных компании.');
                }
            }
        });
    });
    
    
    
    $('.select-company-btn').click(function() {
        var templateId = $(this).data('id');
        
        // Выполняем Ajax-запрос
        $.ajax({
            url: '<?php echo base_url('cabinet/get_recipient_template'); ?>',
            type: 'POST',
            data: {id: templateId},
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    // Заполняем поля в форме данными получателя
                    $('#recipient-company').val(response.company);
                    $('#recipient-name').val(response.recipient_name);
                    $('#recipient-phone').val(response.recipient_phone);
                    $('#recipient-address').val(response.address);
                    $('#recipient-city').val(response.city);
                    $('#recipient-country').val(response.country);
                    $('#recipient-region').val(response.region);
                    $('#recipient-district').val(response.district);
                    
                    // Закрываем модальное окно
                    $('#template-modal').addClass('hidden');
                } else {
                    alert('Ошибка при получении данных компании.');
                }
            }
        });
    });

    // Закрытие модального окна
    $('.close-modal-btn').click(function() {
        $('#template-modal').addClass('hidden');
    });
    $('.close-modal-btn-2').click(function() {
        $('#template-modal-2').addClass('hidden');
    });
    // Устанавливаем сегодняшнюю дату по умолчанию
    var today = new Date();
    var day = String(today.getDate()).padStart(2, '0');
    var month = String(today.getMonth() + 1).padStart(2, '0'); // Январь — это 0!
    var year = today.getFullYear();
    var currentDate = year + '-' + month + '-' + day;

    $('#date').val(currentDate);  // Устанавливаем значение сегодняшней даты в поле
    // Открываем datepicker при клике на поле даты
    $('#date').on('click', function() {
        // Проверяем, поддерживает ли браузер метод showPicker
        if (this.showPicker) {
            this.showPicker();  // Открываем встроенный datepicker
        } else {
            $(this).focus();  // Для браузеров, не поддерживающих showPicker
        }
    });

});
</script>
<?$this->load->view('templates/cabinet/block/footer_view');?>
           