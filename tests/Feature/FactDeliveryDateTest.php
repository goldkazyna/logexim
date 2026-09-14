<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\Staff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class FactDeliveryDateTest extends TestCase
{
    use RefreshDatabase;

    private function invoice(array $attributes = []): Invoice
    {
        return Invoice::create(array_merge([
            'user_id' => 1, 'date' => '2026-09-10', 'invoice_number' => 906592,
            'status' => 2, 'detail_status' => 5,
            'sender_name' => 'Отпр', 'sender_phone' => '+7', 'sender_address' => 'a',
            'sender_city' => 'Алматы', 'sender_country' => 'KZ',
            'recipient_name' => 'Пол', 'recipient_phone' => '+7', 'recipient_address' => 'b',
            'recipient_city' => 'Астана', 'recipient_country' => 'KZ',
            'description' => 'x', 'quantity' => 1, 'weight' => 5, 'declared_value' => 0,
            'fact_date' => null,
        ], $attributes));
    }

    public function test_courier_delivery_sets_fact_date(): void
    {
        $courier = Staff::create([
            'full_name' => 'Агент', 'login' => 'a1', 'password' => sha1(md5('x')),
            'role' => 'agent', 'roles' => ['agent'], 'active' => 1,
        ]);
        $invoice = $this->invoice(['receiving_courier_id' => $courier->id]);
        Sanctum::actingAs($courier);

        $signature = 'data:image/png;base64,' . base64_encode(str_repeat('sig-bytes', 4));

        $this->postJson("/api/staff/invoices/{$invoice->id}/deliver", ['signature' => $signature], ['X-Staff-Role' => 'agent'])
            ->assertOk();

        $invoice->refresh();
        $this->assertNotNull($invoice->fact_date, 'фактическая дата должна проставиться при доставке');
        $this->assertSame(now()->toDateString(), \Carbon\Carbon::parse($invoice->fact_date)->toDateString());
        $this->assertNotNull($invoice->delivered_at, 'время доставки тоже фиксируется');
    }

    public function test_admin_marking_executed_sets_fact_date(): void
    {
        $this->withSession(['admin' => 'admin', 'role' => 'admin', 'roles' => ['admin']]);
        $invoice = $this->invoice(['status' => 0, 'detail_status' => 0]);

        $this->post("/admin/invoices/status/{$invoice->id}", ['status' => 3])
            ->assertRedirect();

        $invoice->refresh();
        $this->assertNotNull($invoice->fact_date);
        $this->assertSame(now()->toDateString(), \Carbon\Carbon::parse($invoice->fact_date)->toDateString());
    }

    public function test_admin_status_change_does_not_overwrite_existing_fact_date(): void
    {
        $this->withSession(['admin' => 'admin', 'role' => 'admin', 'roles' => ['admin']]);
        $invoice = $this->invoice(['status' => 3, 'detail_status' => 6, 'fact_date' => '2026-09-01']);

        // Меняем на другой статус и обратно — руками выставленную дату не трём.
        $this->post("/admin/invoices/status/{$invoice->id}", ['status' => 3]);

        $this->assertSame('2026-09-01', \Carbon\Carbon::parse($invoice->refresh()->fact_date)->toDateString());
    }

    public function test_non_executed_status_leaves_fact_date_empty(): void
    {
        $this->withSession(['admin' => 'admin', 'role' => 'admin', 'roles' => ['admin']]);
        $invoice = $this->invoice(['status' => 0, 'detail_status' => 0]);

        $this->post("/admin/invoices/status/{$invoice->id}", ['status' => 2]);

        $this->assertNull($invoice->refresh()->fact_date);
    }
}
