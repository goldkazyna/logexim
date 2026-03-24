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
            <h4 class="text-slate-900 text-lg font-medium mb-2">Настройка профиля</h4>

            <div class="md:flex hidden items-center gap-3 text-sm font-semibold">
                <p>Пожалуйста, обновите свои данные, если это необходимо. Все изменения будут сохранены.
                <br />
                <span style="color: red; font-weight: bold;">БИН изменять нельзя!</span>
                <br />
                <span style="color: red; font-weight: bold;">Указывайте корректный Email. В случае, если вы забудете пароль, вы сможете восстановить доступ через электронную почту.</span>
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
                <form action="<?php echo base_url('cabinet/update_profile'); ?>" method="post" class="mt-4">
                    <div class="mb-4">
                        <label for="name" class="text-gray-800 font-bold text-base inline-block mb-2">Имя</label>
                        <input type="text" id="name" name="director_name" class="form-input w-full md:w-1/2" placeholder="Ваше имя" value="<?php echo $user['director_name']; ?>" required>
                    </div>

                    <div class="mb-4">
                        <label for="email" class="text-gray-800 font-bold text-base inline-block mb-2">Email</label>
                        <input type="email" id="email" name="email" class="form-input w-full md:w-1/2" placeholder="Ваш email" value="<?php echo $user['email']; ?>" required>
                    </div>

                    <div class="mb-4">
                        <label for="phone" class="text-gray-800 font-bold text-base inline-block mb-2">Телефон</label>
                        <input type="text" id="phone" name="phone" class="form-input w-full md:w-1/2" placeholder="Ваш телефон" value="<?php echo $user['phone']; ?>" required>
                    </div>

                    <div class="mb-4">
                        <label for="company" class="text-gray-800 font-bold text-base inline-block mb-2">Компания</label>
                        <input type="text" id="company" name="company_name" class="form-input w-full md:w-1/2" placeholder="Компания" value='<?php echo $user['company_name']; ?>'  >
                    </div>

                    <div class="mb-4">
                        <label for="city" class="text-gray-800 font-bold text-base inline-block mb-2">Город</label>
                        <input type="text" id="city" name="city" class="form-input w-full md:w-1/2"  value="<?php echo $user['city']; ?>">
                    </div>

                    <div class="mb-4">
                        <label for="country" class="text-gray-800 font-bold text-base inline-block mb-2">Страна</label>
                        <input type="text" id="country" name="country" class="form-input w-full md:w-1/2" value="<?php echo $user['country']; ?>">
                    </div>

                    <div class="mb-4">
                        <label for="region" class="text-gray-800 font-bold text-base inline-block mb-2">Область</label>
                        <input type="text" id="region" name="region" class="form-input w-full md:w-1/2"  value="<?php echo $user['region']; ?>">
                    </div>

                    <div class="mb-4">
                        <label for="district" class="text-gray-800 font-bold text-base inline-block mb-2">Район</label>
                        <input type="text" id="district" name="district" class="form-input w-full md:w-1/2" value="<?php echo $user['district']; ?>">
                    </div>

                    <div class="mb-4">
                        <label for="address" class="text-gray-800 font-bold text-base inline-block mb-2">Адрес</label>
                        <textarea id="address" name="address" class="form-input w-full md:w-1/2" rows="3" placeholder="Адрес"><?php echo $user['address']; ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary common_btn">Сохранить изменения</button>
                </form>
            </div>
        </div>
    </div>
    <!-- Page Title End -->
</main>

<!-- JavaScript для скрытия сообщения через 5 секунд -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        // Показать сообщение на 5 секунд, затем плавно скрыть его
        setTimeout(function() {
            $('#success-alert').fadeOut('slow');
        }, 5000);
    });
</script>
<?$this->load->view('templates/cabinet/block/footer_view');?>
           