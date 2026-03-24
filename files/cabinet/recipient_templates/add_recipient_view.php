<?$this->load->view('templates/cabinet/block/header_view');?>
<style>
.common_btn{
    background-color: #D0171C; border-color: #D0171C; border-radius: 10px; color:#ffffff;
}
.common_btn:hover{
    background-color: #a21216;
}

</style>

            <main class="flex-grow p-6">

    <!-- Page Title Start -->
    <div class="flex items-center justify-between flex-wrap gap-2 mb-6">
        <div>
        
        <div class="card overflow-hidden">
                        <div class="card-header">
                            <h4 class="text-slate-900 text-lg font-medium mb-2">Шаблон отправителя</h4>
                        </div>
                        <div>
                            <div class="overflow-x-auto">
                                <div class="min-w-full inline-block align-middle">
                                    <div class="overflow-hidden" style="padding: 20px;">
                                        <p>Пожалуйста, заполните все поля для сохранения данных в шаблоне отправителей. В будущем вы сможете выбрать этот шаблон для быстрого и удобного заполнения накладных, экономя ваше время.</p><br />
                                        
                                        <div class="">
                <form action="<?php echo base_url('cabinet/save_recipient_template'); ?>" method="post" class="mt-4">
                    <div class="mb-4">
                        <label for="recipient_name" class="text-gray-800 font-bold text-base inline-block mb-2">ФИО получателя</label>
                        <input type="text" id="recipient_name" name="recipient_name" class="form-input w-full md:w-1/2" placeholder="ФИО получателя" required>
                    </div>

                    <div class="mb-4">
                        <label for="recipient_phone" class="text-gray-800 font-bold text-base inline-block mb-2">Телефон получателя</label>
                        <input type="text" id="recipient_phone" name="recipient_phone" class="form-input w-full md:w-1/2" placeholder="Телефон получателя" required>
                    </div>

                    <div class="mb-4">
                        <label for="company" class="text-gray-800 font-bold text-base inline-block mb-2">Компания</label>
                        <input type="text" id="company" name="company" class="form-input w-full md:w-1/2" placeholder="Компания">
                    </div>

                    <div class="mb-4">
                        <label for="city" class="text-gray-800 font-bold text-base inline-block mb-2">Город</label>
                        <input type="text" id="city" name="city" class="form-input w-full md:w-1/2" placeholder="Город">
                    </div>

                    <div class="mb-4">
                        <label for="country" class="text-gray-800 font-bold text-base inline-block mb-2">Страна</label>
                        <input type="text" id="country" name="country" class="form-input w-full md:w-1/2" placeholder="Страна">
                    </div>

                    <div class="mb-4">
                        <label for="region" class="text-gray-800 font-bold text-base inline-block mb-2">Область</label>
                        <input type="text" id="region" name="region" class="form-input w-full md:w-1/2" placeholder="Область">
                    </div>

                    <div class="mb-4">
                        <label for="district" class="text-gray-800 font-bold text-base inline-block mb-2">Район</label>
                        <input type="text" id="district" name="district" class="form-input w-full md:w-1/2" placeholder="Район">
                    </div>

                    <div class="mb-4">
                        <label for="address" class="text-gray-800 font-bold text-base inline-block mb-2">Адрес</label>
                        <textarea id="address" name="address" class="form-input w-full md:w-1/2" rows="3" placeholder="Адрес"></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary common_btn">Сохранить отправителя</button>
                </form>
            </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
        
            
        </div>
    </div>
    <!-- Page Title End -->
</main>

<?$this->load->view('templates/cabinet/block/footer_view');?>
           