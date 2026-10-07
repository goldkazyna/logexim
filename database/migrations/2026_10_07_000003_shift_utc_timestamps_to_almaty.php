<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * До этой правки Laravel писал время в UTC, а MySQL на сервере живёт в +05
 * (created_at накладной ставит база). Время, которое ставил PHP, отставало на
 * 5 часов. Теперь приложение в Asia/Almaty — сдвигаем уже записанное.
 * Эти колонки появились вместе с Laravel, поэтому все значения в них — UTC.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return; // тестовая sqlite-база пустая
        }

        DB::statement('UPDATE invoice_events SET created_at = created_at + INTERVAL 5 HOUR WHERE created_at IS NOT NULL');
        DB::statement('UPDATE invoices SET
            received_at = received_at + INTERVAL 5 HOUR,
            shipped_at = shipped_at + INTERVAL 5 HOUR,
            delivered_at = delivered_at + INTERVAL 5 HOUR');

        // Фактическая дата доставки, поставленная автоматически при доставке,
        // могла уйти на предыдущий день — пересчитываем от исправленного времени.
        DB::statement('UPDATE invoices SET fact_date = DATE(delivered_at)
            WHERE delivered_at IS NOT NULL AND fact_date = DATE(delivered_at - INTERVAL 5 HOUR)');
    }

    public function down(): void
    {
    }
};
