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
    .date-filter {
        margin-bottom: 20px;
        display: flex;
        gap: 20px;
        align-items: center;
    }
</style>

<main class="flex-grow p-6">

    <!-- Page Title Start -->
    <div class="flex items-center justify-between flex-wrap gap-2 mb-6">
        <div>
            <div class="card overflow-hidden">
                <div class="card-header">
                    <h4 class="text-slate-900 text-lg font-medium mb-2">Отчеты по накладным</h4>
                </div>
                <div class="p-4">
                    <form method="GET" action="<?php echo base_url('cabinet/reports'); ?>">
                        <div class="date-filter">
                            <label for="start_date">Дата начала:</label>
                            <input type="date" name="start_date" id="start_date" class="form-input" required>
                            
                            <label for="end_date">Дата окончания:</label>
                            <input type="date" name="end_date" id="end_date" class="form-input" required>
                            
                            <button type="submit" class="btn btn-primary common_btn">Поиск</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Display Invoices -->
            <div class="card overflow-hidden mt-4">
                <div class="card-header">
                    <h4 class="card-title">Результаты поиска</h4>
                </div>
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
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <?php if (!empty($invoices)): ?>
                                        <?php foreach ($invoices as $invoice): ?>
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                                    <?php echo date('d.m.Y', strtotime($invoice['date'])); ?>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                                    <?php echo $invoice['invoice_number']; ?>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                                    <?php echo $invoice['sender_company']; ?><br>
                                                    <?php echo $invoice['sender_name']; ?>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                                    <?php echo $invoice['recipient_company']; ?><br>
                                                    <?php echo $invoice['recipient_name']; ?>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                                    <?php echo $invoice['quantity']; ?>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                                    <?php echo $invoice['weight']; ?>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                                    <?php echo $invoice['declared_value']; ?> KZT
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
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
                                                                echo 'Исполнена';
                                                                break;
                                                            default:
                                                                echo 'Неизвестный статус';
                                                                break;
                                                        }
                                                    ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="9" class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800">Накладные не найдены.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <?php
                $start_date = isset($start_date) ? $start_date : '';
                $end_date = isset($end_date) ? $end_date : '';
            ?>
            <!-- Export Button -->
            <?php if (!empty($invoices)): ?>
                <div class="mt-4 text-right">
                    <form method="GET" action="<?php echo base_url('cabinet/export_invoices'); ?>">
                        <input type="hidden" name="start_date" value="<?php echo $start_date; ?>">
                        <input type="hidden" name="end_date" value="<?php echo $end_date; ?>">
                        <button type="submit" class="btn btn-primary common_btn">Экспорт в Excel</button>
                    </form>
                </div>
            <?php endif; ?>
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
