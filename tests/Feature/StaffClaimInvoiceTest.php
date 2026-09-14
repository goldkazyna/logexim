<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\Staff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class StaffClaimInvoiceTest extends TestCase
{
    use RefreshDatabase;

    private function staff(string $role = 'courier', string $login = 'c1'): Staff
    {
        return Staff::create([
            'full_name' => "Сотрудник $login", 'login' => $login, 'password' => sha1(md5('x')),
            'role' => $role, 'roles' => [$role], 'active' => 1,
        ]);
    }

    private function invoice(array $attributes = []): Invoice
    {
        static $n = 903000;
        $n++;

        return Invoice::create(array_merge([
            'user_id' => 1, 'date' => '2026-09-14', 'invoice_number' => $n,
            'status' => 0, 'detail_status' => 0,
            'sender_name' => 'Отпр', 'sender_phone' => '+7', 'sender_address' => 'a',
            'sender_city' => 'Алматы', 'sender_country' => 'KZ',
            'recipient_name' => 'Пол', 'recipient_phone' => '+7', 'recipient_address' => 'b',
            'recipient_city' => 'Астана', 'recipient_country' => 'KZ',
            'description' => 'x', 'quantity' => 1, 'weight' => 5, 'declared_value' => 0,
        ], $attributes));
    }

    private function sig(): string
    {
        return 'data:image/png;base64,' . base64_encode(str_repeat('sig-bytes', 4));
    }

    public function test_courier_can_view_an_unassigned_invoice_by_number(): void
    {
        $courier = $this->staff();
        $inv = $this->invoice(); // никому не назначена
        Sanctum::actingAs($courier);

        $this->getJson("/api/staff/invoices/by-number/{$inv->invoice_number}")
            ->assertOk()
            ->assertJsonPath('invoice.number', $inv->invoice_number);
    }

    public function test_pickup_claims_unassigned_invoice(): void
    {
        $courier = $this->staff();
        $inv = $this->invoice(); // courier_id пуст
        Sanctum::actingAs($courier);

        $this->postJson("/api/staff/invoices/{$inv->id}/pickup", ['signature' => $this->sig()], ['X-Staff-Role' => 'courier'])
            ->assertOk();

        $inv->refresh();
        $this->assertSame($courier->id, (int) $inv->courier_id, 'курьер назначился сам при заборе');
        $this->assertSame(2, (int) $inv->detail_status);
    }

    public function test_pickup_rejected_if_already_claimed_by_another(): void
    {
        $mine = $this->staff('courier', 'c1');
        $other = $this->staff('courier', 'c2');
        $inv = $this->invoice(['courier_id' => $other->id]);
        Sanctum::actingAs($mine);

        $this->postJson("/api/staff/invoices/{$inv->id}/pickup", ['signature' => $this->sig()], ['X-Staff-Role' => 'courier'])
            ->assertForbidden();

        $this->assertSame($other->id, (int) $inv->refresh()->courier_id);
    }

    public function test_pickup_still_works_for_the_assigned_courier(): void
    {
        $courier = $this->staff();
        $inv = $this->invoice(['courier_id' => $courier->id, 'detail_status' => 1]);
        Sanctum::actingAs($courier);

        $this->postJson("/api/staff/invoices/{$inv->id}/pickup", ['signature' => $this->sig()], ['X-Staff-Role' => 'courier'])
            ->assertOk();

        $this->assertSame(2, (int) $inv->refresh()->detail_status);
    }

    public function test_destination_pickup_claims_unassigned_invoice(): void
    {
        $agent = $this->staff('agent', 'a1');
        $inv = $this->invoice(['detail_status' => 4]); // отправлено со склада, приёмник не назначен
        Sanctum::actingAs($agent);

        $this->postJson("/api/staff/invoices/{$inv->id}/destination-pickup", [], ['X-Staff-Role' => 'agent'])
            ->assertOk();

        $inv->refresh();
        $this->assertSame($agent->id, (int) $inv->receiving_courier_id);
        $this->assertSame(5, (int) $inv->detail_status);
    }

    public function test_destination_pickup_rejected_if_claimed_by_another(): void
    {
        $me = $this->staff('agent', 'a1');
        $other = $this->staff('agent', 'a2');
        $inv = $this->invoice(['detail_status' => 4, 'receiving_courier_id' => $other->id]);
        Sanctum::actingAs($me);

        $this->postJson("/api/staff/invoices/{$inv->id}/destination-pickup", [], ['X-Staff-Role' => 'agent'])
            ->assertForbidden();
    }
}
