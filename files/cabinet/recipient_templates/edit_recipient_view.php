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
            <h4 class="text-slate-900 text-lg font-medium mb-2">Шаблон отправителей</h4>

            <div class="md:flex hidden items-center gap-3 text-sm font-semibold">
                <p>Пожалуйста, заполните все поля для сохранения данных в шаблоне отправителей. В будущем вы сможете выбрать этот шаблон для быстрого и удобного заполнения накладных, экономя ваше время.</p>
                <br />
            </div>
            <div class="">
                <form action="<?php echo base_url('cabinet/update_recipient_template'); ?>" method="post" class="mt-4">
                    <input type="hidden" name="id" value="<?php echo $template['id']; ?>">
                
                    <div class="mb-4">
                        <label for="recipient_name" class="text-gray-800 font-bold text-base inline-block mb-2">ФИО получателя</label>
                        <input type="text" id="recipient_name" name="recipient_name" class="form-input w-full md:w-1/2" value="<?php echo $template['recipient_name']; ?>" placeholder="ФИО получателя" required>
                    </div>
                
                    <div class="mb-4">
                        <label for="recipient_phone" class="text-gray-800 font-bold text-base inline-block mb-2">Телефон получателя</label>
                        <input type="text" id="recipient_phone" name="recipient_phone" class="form-input w-full md:w-1/2" value="<?php echo $template['recipient_phone']; ?>" placeholder="Телефон получателя" required>
                    </div>
                
                    <div class="mb-4">
                        <label for="company" class="text-gray-800 font-bold text-base inline-block mb-2">Компания</label>
                        <input type="text" id="company" name="company" class="form-input w-full md:w-1/2" value="<?php echo $template['company']; ?>" placeholder="Компания">
                    </div>
                
                    <div class="mb-4">
                        <label for="city" class="text-gray-800 font-bold text-base inline-block mb-2">Город</label>
                        <input type="text" id="city" name="city" class="form-input w-full md:w-1/2" value="<?php echo $template['city']; ?>" placeholder="Город">
                    </div>
                
                    <div class="mb-4">
                        <label for="country" class="text-gray-800 font-bold text-base inline-block mb-2">Страна</label>
                        <input type="text" id="country" name="country" class="form-input w-full md:w-1/2" value="<?php echo $template['country']; ?>" placeholder="Страна">
                    </div>
                
                    <div class="mb-4">
                        <label for="region" class="text-gray-800 font-bold text-base inline-block mb-2">Область</label>
                        <input type="text" id="region" name="region" class="form-input w-full md:w-1/2" value="<?php echo $template['region']; ?>" placeholder="Область">
                    </div>
                
                    <div class="mb-4">
                        <label for="district" class="text-gray-800 font-bold text-base inline-block mb-2">Район</label>
                        <input type="text" id="district" name="district" class="form-input w-full md:w-1/2" value="<?php echo $template['district']; ?>" placeholder="Район">
                    </div>
                
                    <div class="mb-4">
                        <label for="address" class="text-gray-800 font-bold text-base inline-block mb-2">Адрес</label>
                        <textarea id="address" name="address" class="form-input w-full md:w-1/2" rows="3" placeholder="Адрес"><?php echo $template['address']; ?></textarea>
                    </div>
                
                    <button type="submit" class="btn btn-primary common_btn">Сохранить изменения</button>
                </form>

            </div>
        </div>
    </div>
    <!-- Page Title End -->
</main>

<?$this->load->view('templates/cabinet/block/footer_view');?>
           