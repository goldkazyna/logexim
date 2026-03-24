<?php $this->load->view('templates/cabinet/block/header_view'); ?>

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

<style>
    .common_btn {
        background-color: #D0171C;
        border-color: #D0171C;
        border-radius: 10px;
        color: #ffffff;
    }
    .common_btn:hover {
        background-color: #a21216;
    }
    .icon-btn {
        font-size: 20px;
        cursor: pointer;
        color: #D0171C;
    }
    .icon-btn:hover {
        color: #a21216;
    }
</style>

<main class="flex-grow p-6">

    <!-- Page Title Start -->
    <div class="flex items-center justify-between flex-wrap gap-2 mb-6">
        <div>
            <div class="card overflow-hidden">
                <div class="card-header">
                    <h4 class="text-slate-900 text-lg font-medium mb-2">Накладные</h4>
                </div>
                <div>
                    <div class="overflow-x-auto">
                        <div class="min-w-full inline-block align-middle">
                            <div class="overflow-hidden" style="padding: 20px;">
                                <p><b>Накладная</b> — это документ для отправки товаров. В этом разделе вы можете просмотреть свои накладные и управлять ими.</p>
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
                <a href="<?php echo base_url('cabinet/create_invoice'); ?>" class="btn btn-primary common_btn">Добавить накладную</a>
            </div>
            <br />

            <div class="card overflow-hidden">
                <div class="card-header">
                    <h4 class="card-title">Список накладных</h4>
                </div>
                <div>
                    <div class="overflow-x-auto">
                        <div class="min-w-full inline-block align-middle">
                            <div class="overflow-hidden">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead>
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-start text-sm text-gray-500">Дата</th>
                                            <th scope="col" class="px-6 py-3 text-start text-sm text-gray-500">Номер накладной</th>
                                            <th scope="col" class="px-6 py-3 text-start text-sm text-gray-500">Отправитель</th>
                                            <th scope="col" class="px-6 py-3 text-start text-sm text-gray-500">Получатель</th>
                                            <th scope="col" class="px-6 py-3 text-start text-sm text-gray-500">Количество мест</th>
                                            <th scope="col" class="px-6 py-3 text-start text-sm text-gray-500">Вес (кг)</th>
                                            <th scope="col" class="px-6 py-3 text-start text-sm text-gray-500">Объявленная ценность</th>
                                            <th scope="col" class="px-6 py-3 text-start text-sm text-gray-500">Статус</th>
                                            <th scope="col" class="px-6 py-3 text-end text-sm text-gray-500">Действие</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        <?php if (!empty($invoices)): ?>
                                            <?php foreach ($invoices as $invoice): ?>
                                                <tr id="invoice-<?php echo $invoice['id']; ?>">
                                                    <!-- Форматирование даты в дд.мм.гггг -->
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                                        <?php echo date('d.m.Y', strtotime($invoice['date'])); ?>
                                                    </td>

                                                    <!-- Номер накладной -->
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                                        <?php echo $invoice['invoice_number']; ?>
                                                    </td>

                                                    <!-- Отправитель: компания, ФИО, телефон -->
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                                        <?php echo $invoice['sender_company']; ?><br>
                                                        <?php echo $invoice['sender_name']; ?><br>
                                                        <?php echo $invoice['sender_phone']; ?>
                                                    </td>

                                                    <!-- Получатель: компания, ФИО, телефон -->
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                                        <?php echo $invoice['recipient_company']; ?><br>
                                                        <?php echo $invoice['recipient_name']; ?><br>
                                                        <?php echo $invoice['recipient_phone']; ?>
                                                    </td>

                                                    <!-- Количество мест -->
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                                        <?php echo $invoice['quantity']; ?>
                                                    </td>

                                                    <!-- Вес -->
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                                        <?php echo $invoice['weight']; ?>
                                                    </td>

                                                    <!-- Объявленная ценность -->
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                                        <?php echo $invoice['declared_value']; ?> KZT
                                                    </td>

                                                    <!-- Статус накладной -->
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                                        <?php 
                                                            switch ($invoice['status']) {
                                                                case 0:
                                                                    echo '<span style="background-color:#00056d; color:#ffffff; padding:10px; border-radius:10px;">Заявка создана</span>';
                                                                    break;
                                                                case 1:
                                                                    echo '<span style="background-color:#ffcc00; color:#ffffff; padding:10px; border-radius:10px;">Принята в работу</span>';
                                                                    break;
                                                                case 2:
                                                                    echo '<span style="background-color:#00aaff; color:#ffffff; padding:10px; border-radius:10px;">Отправлено</span>';
                                                                    break;
                                                                case 3:
                                                                    echo '<span style="background-color:#28a745; color:#ffffff; padding:10px; border-radius:10px;">Исполнена</span>';
                                                                    break;
                                                                case 4:
                                                                    echo '<span style="background-color:red; color:#ffffff; padding:10px; border-radius:10px;">Отменена</span>';
                                                                    break;    
                                                                default:
                                                                    echo 'Неизвестный статус';
                                                                    break;
                                                            }
                                                        ?>
                                                    </td>

                                                    <!-- Действие -->
                                                    <td class="px-6 py-4 whitespace-nowrap text-end text-sm font-medium" align="center">
                                                        <a href="<?php echo base_url('cabinet/view_invoice/' . $invoice['id']); ?>" class="icon-btn">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="<?php echo base_url('cabinet/print_invoice/' . $invoice['id']); ?>" class="icon-btn ml-4">
                                                            <i class="fas fa-print"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="9" class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800">Накладные отсутствуют.</td>
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

<?php $this->load->view('templates/cabinet/block/footer_view'); ?>
