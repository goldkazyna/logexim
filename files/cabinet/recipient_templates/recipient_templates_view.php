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
<p><b>Шаблон отправителя</b> — это инструмент для ускорения процесса создания накладных. <br />Когда вы заполняете накладную, необходимо ввести данные отправителя: его имя, контактную информацию, адрес и другие детали. <br />Чтобы не вводить эти данные каждый раз заново, вы можете воспользоваться шаблоном отправителя.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                        <div class="mt-4">
                           <?php if ($this->session->flashdata('success_message')): ?>
        <div id="success-alert" class="bg-primary text-sm text-white rounded-md p-4 mb-4" role="alert">
            <span class="font-bold">Успешно!</span> <?php echo $this->session->flashdata('success_message'); ?>
        </div>
    <?php endif; ?>
                        </div>
                        <div class="mt-4">
                            <a href="<?php echo base_url('cabinet/add_recipient_templates'); ?>" class="btn btn-primary common_btn">Добавить отправителя</a>

                        </div>
                        <br />
                        <div class="card overflow-hidden">
    <div class="card-header">
        <h4 class="card-title">Шаблоны отправителей</h4>
    </div>
    <div>
        <div class="overflow-x-auto">
            <div class="min-w-full inline-block align-middle">
                <div class="overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th scope="col" class="px-6 py-3 text-start text-sm text-gray-500">ФИО</th>
                                <th scope="col" class="px-6 py-3 text-start text-sm text-gray-500">Телефон</th>
                                <th scope="col" class="px-6 py-3 text-start text-sm text-gray-500">Компания</th>
                                <th scope="col" class="px-6 py-3 text-start text-sm text-gray-500">Город</th>
                                <th scope="col" class="px-6 py-3 text-start text-sm text-gray-500">Страна</th>
                                <th scope="col" class="px-6 py-3 text-start text-sm text-gray-500">Адрес</th>
                                <th scope="col" class="px-6 py-3 text-end text-sm text-gray-500">Действие</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php if (!empty($templates)): ?>
                                <?php foreach ($templates as $template): ?>
                                    <tr id="template-<?php echo $template['id']; ?>">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800"><?php echo $template['recipient_name']; ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800"><?php echo $template['recipient_phone']; ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800"><?php echo $template['company']; ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800"><?php echo $template['city']; ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800"><?php echo $template['country']; ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800"><?php echo $template['address']; ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-end text-sm font-medium">
                                            <a href="/cabinet/edit_recipient_templates/<?echo $template['id']?>" class="text-primary hover:text-sky-700 cursor-pointer>">Редактировать</a> / 
                                            <a class="text-primary hover:text-sky-700 cursor-pointer delete-template" data-id="<?php echo $template['id']; ?>">Удалить</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800">Шаблоны отправителей отсутствуют.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
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



<script>
    $(document).ready(function() {
        // Удаление шаблона через Ajax
        $('.delete-template').click(function() {
            var templateId = $(this).data('id');
            var row = $('#template-' + templateId);
            
            $.ajax({
                url: '<?php echo base_url("cabinet/delete_recipient_template"); ?>',
                type: 'POST',
                data: {id: templateId},
                success: function(response) {
                    var result = JSON.parse(response);
                    if (result.status === 'success') {
                        row.fadeOut('slow', function() {
                            row.remove();
                        });
                        // Показать сообщение об успешном удалении
                        $('body').append('<div id="delete-alert" class="bg-primary text-sm text-white rounded-md p-4 mb-4" role="alert">' +
                            '<span class="font-bold">Успешно!</span> Шаблон отправителя удален.' +
                            '</div>');
                        // Скрыть сообщение через 5 секунд
                        setTimeout(function() {
                            $('#delete-alert').fadeOut('slow', function() {
                                $(this).remove();
                            });
                        }, 5000);
                    } else {
                        alert('Ошибка при удалении шаблона');
                    }
                }
            });
        });
    });
</script>
<?$this->load->view('templates/cabinet/block/footer_view');?>

           