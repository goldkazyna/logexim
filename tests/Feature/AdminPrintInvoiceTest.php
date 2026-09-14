<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\Staff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPrintInvoiceTest extends TestCase
{
    use RefreshDatabase;

    private function invoice(array $attributes = []): Invoice
    {
        return Invoice::create(array_merge([
            'user_id' => 1, 'date' => '2026-09-14', 'invoice_number' => 906592,
            'status' => 0, 'detail_status' => 0,
            'sender_name' => 'Отпр', 'sender_phone' => '+7', 'sender_address' => 'a',
            'sender_city' => 'Алматы', 'sender_country' => 'KZ', 'sender_company' => 'ТОО Отпр',
            'recipient_name' => 'Пол', 'recipient_phone' => '+7', 'recipient_address' => 'b',
            'recipient_city' => 'Астана', 'recipient_country' => 'KZ', 'recipient_company' => 'ТОО Пол',
            'description' => 'x', 'quantity' => 1, 'weight' => 5, 'declared_value' => 0,
        ], $attributes));
    }

    public function test_admin_can_open_the_print_page(): void
    {
        $this->withSession(['admin' => 'admin', 'role' => 'admin', 'roles' => ['admin']]);
        $inv = $this->invoice();

        $this->get("/admin/invoices/print/{$inv->id}")
            ->assertOk()
            ->assertSee('НАКЛАДНАЯ № 906592')
            ->assertSee('window.print()', false);
    }

    public function test_view_page_has_a_print_link(): void
    {
        $this->withSession(['admin' => 'admin', 'role' => 'admin', 'roles' => ['admin']]);
        $inv = $this->invoice();

        $this->get("/admin/invoices/view/{$inv->id}")
            ->assertOk()
            ->assertSee("/admin/invoices/print/{$inv->id}");
    }

    public function test_guest_cannot_print(): void
    {
        $inv = $this->invoice();
        $this->get("/admin/invoices/print/{$inv->id}")->assertRedirect('/admin');
    }

    public function test_courier_cannot_print_someone_elses_invoice(): void
    {
        $courier = Staff::create([
            'full_name' => 'Курьер', 'login' => 'c1', 'password' => sha1(md5('x')),
            'role' => 'courier', 'roles' => ['courier'], 'active' => 1,
        ]);
        $inv = $this->invoice(['courier_id' => 999]);
        $this->withSession(['staff_id' => $courier->id, 'role' => 'courier', 'roles' => ['courier']]);

        $this->get("/admin/invoices/print/{$inv->id}")->assertRedirect('/admin/invoices');
    }
}
