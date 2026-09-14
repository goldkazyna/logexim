<?php

namespace Tests\Feature;

use App\Models\Invoice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminInvoicesListTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withSession(['admin' => 'admin', 'role' => 'admin', 'roles' => ['admin']]);
    }

    private function invoice(array $attributes = []): Invoice
    {
        static $n = 903000;
        $n++;

        return Invoice::create(array_merge([
            'user_id' => 1, 'date' => '2026-09-14', 'invoice_number' => $n,
            'status' => 0, 'detail_status' => 0,
            'sender_name' => 'Отпр', 'sender_phone' => '+7', 'sender_address' => 'a',
            'sender_city' => 'Алматы', 'sender_country' => 'KZ', 'sender_company' => 'ТОО Отпр',
            'recipient_name' => 'Пол', 'recipient_phone' => '+7', 'recipient_address' => 'b',
            'recipient_city' => 'Астана', 'recipient_country' => 'KZ', 'recipient_company' => 'ТОО Пол',
            'description' => 'x', 'quantity' => 1, 'weight' => 5, 'declared_value' => 0,
        ], $attributes));
    }

    public function test_list_shows_stage_progress_and_no_status_badge(): void
    {
        $this->invoice(['status' => 3, 'detail_status' => 4]);

        $html = $this->get('/admin/invoices')->assertOk()->getContent();

        $this->assertStringContainsString('stage-prog__dots', $html);
        $this->assertStringContainsString('Этап доставки', $html);
        // Колонки «Статус» и её бейджей больше нет.
        $this->assertStringNotContainsString('status-badge', $html);
        $this->assertStringNotContainsString('<th>Статус</th>', $html);
    }

    public function test_cancelled_invoice_shows_a_cancelled_chip_not_progress(): void
    {
        $this->invoice(['status' => 4, 'detail_status' => 0]);

        $html = $this->get('/admin/invoices')->assertOk()->getContent();

        $this->assertStringContainsString('stage-cancelled', $html);
    }

    public function test_delivered_legacy_invoice_reads_as_delivered_stage(): void
    {
        // status=3 (Исполнена), detail_status=0 — таких большинство.
        $this->invoice(['status' => 3, 'detail_status' => 0]);

        $html = $this->get('/admin/invoices')->assertOk()->getContent();

        // Этап выведен из статуса → «Доставлено», а не «Заявка создана».
        $this->assertStringContainsString('Доставлено', $html);
        $this->assertStringNotContainsString('Заявка создана', $html);
    }
}
