<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\InvoiceEvent;
use App\Models\Staff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class StaffActiveRoleTest extends TestCase
{
    use RefreshDatabase;

    private const HEADER = 'X-Staff-Role';

    private function staff(string $primary, array $roles): Staff
    {
        static $n = 0;
        $n++;

        return Staff::create([
            'full_name' => "Сотрудник $n",
            'login' => "staff$n",
            'password' => sha1(md5('secret')),
            'role' => $primary,
            'roles' => $roles,
            'phone' => '+77010000000',
            'email' => "staff$n@example.com",
            'active' => 1,
        ]);
    }

    private function invoice(array $attributes = []): Invoice
    {
        static $number = 903000;
        $number++;

        return Invoice::create(array_merge([
            'user_id' => 1,
            'date' => '2026-08-12',
            'invoice_number' => $number,
            'status' => 1,
            'detail_status' => 0,
            'sender_name' => 'Иванов',
            'sender_phone' => '+77010000000',
            'sender_address' => 'ул. Абая 1',
            'sender_city' => 'Алматы',
            'sender_country' => 'Казахстан',
            'recipient_name' => 'Петров',
            'recipient_phone' => '+77020000000',
            'recipient_address' => 'ул. Достык 5',
            'recipient_city' => 'Астана',
            'recipient_country' => 'Казахстан',
            'description' => 'Запчасти',
            'quantity' => 3,
            'weight' => 20,
            'declared_value' => 0,
        ], $attributes));
    }

    public function test_login_returns_the_whole_role_set(): void
    {
        $this->staff('courier', ['courier', 'warehouse']);

        $this->postJson('/api/staff/auth', ['login' => 'staff1', 'password' => 'secret'])
            ->assertOk()
            ->assertJsonPath('user.role', 'courier')
            ->assertJsonPath('user.roles', ['courier', 'warehouse']);
    }

    public function test_without_the_header_a_multi_role_staff_works_as_its_primary_role(): void
    {
        $staff = $this->staff('courier', ['courier', 'warehouse']);
        $mine = $this->invoice(['courier_id' => $staff->id, 'detail_status' => 1]);
        $this->invoice(['detail_status' => 2]); // на приёмку — курьеру не видна
        Sanctum::actingAs($staff);

        $response = $this->getJson('/api/staff/invoices')->assertOk();

        $this->assertSame([$mine->invoice_number], array_column($response->json('invoices'), 'number'));
    }

    public function test_the_header_switches_which_invoices_are_listed(): void
    {
        $staff = $this->staff('courier', ['courier', 'warehouse']);
        $this->invoice(['courier_id' => $staff->id, 'detail_status' => 1]);
        $toReceive = $this->invoice(['detail_status' => 2]);
        Sanctum::actingAs($staff);

        $response = $this->getJson('/api/staff/invoices', [self::HEADER => 'warehouse'])->assertOk();

        $this->assertSame([$toReceive->invoice_number], array_column($response->json('invoices'), 'number'));
    }

    public function test_a_role_the_staff_does_not_have_is_refused(): void
    {
        $staff = $this->staff('courier', ['courier']);
        Sanctum::actingAs($staff);

        $this->getJson('/api/staff/invoices', [self::HEADER => 'warehouse'])->assertForbidden();
        $this->getJson('/api/staff/invoices', [self::HEADER => 'wizard'])->assertForbidden();
    }

    public function test_warehouse_actions_need_the_warehouse_role_to_be_active(): void
    {
        $staff = $this->staff('courier', ['courier', 'warehouse']);
        $invoice = $this->invoice(['detail_status' => 2]);
        Sanctum::actingAs($staff);

        // Роль есть, но активна курьерская — складское действие закрыто.
        $this->postJson("/api/staff/invoices/{$invoice->id}/receive", [], [self::HEADER => 'courier'])
            ->assertForbidden();

        $this->postJson("/api/staff/invoices/{$invoice->id}/receive", [], [self::HEADER => 'warehouse'])
            ->assertOk();

        $this->assertSame(3, (int) $invoice->refresh()->detail_status);
    }

    public function test_courier_actions_need_a_field_role_to_be_active(): void
    {
        $staff = $this->staff('warehouse', ['warehouse', 'courier']);
        $invoice = $this->invoice(['courier_id' => $staff->id, 'detail_status' => 1]);
        Sanctum::actingAs($staff);

        $signature = 'data:image/png;base64,' . base64_encode(str_repeat('signature-bytes', 4));

        $this->postJson("/api/staff/invoices/{$invoice->id}/pickup", ['signature' => $signature], [self::HEADER => 'warehouse'])
            ->assertForbidden();

        $this->postJson("/api/staff/invoices/{$invoice->id}/pickup", ['signature' => $signature], [self::HEADER => 'courier'])
            ->assertOk();
    }

    public function test_warehouse_dashboard_needs_the_warehouse_role_active(): void
    {
        $staff = $this->staff('courier', ['courier', 'warehouse']);
        Sanctum::actingAs($staff);

        $this->getJson('/api/staff/dashboard', [self::HEADER => 'courier'])->assertForbidden();
        $this->getJson('/api/staff/dashboard', [self::HEADER => 'warehouse'])->assertOk();
        $this->getJson('/api/staff/history', [self::HEADER => 'warehouse'])->assertOk();
    }

    public function test_cargo_editing_needs_the_warehouse_role_active(): void
    {
        $staff = $this->staff('courier', ['courier', 'warehouse']);
        $invoice = $this->invoice(['detail_status' => 3]);
        Sanctum::actingAs($staff);

        $payload = ['quantity' => 5, 'weight' => 41.5];

        $this->postJson("/api/staff/invoices/{$invoice->id}/cargo", $payload, [self::HEADER => 'courier'])
            ->assertForbidden();

        $this->postJson("/api/staff/invoices/{$invoice->id}/cargo", $payload, [self::HEADER => 'warehouse'])
            ->assertOk();
    }

    public function test_journal_records_the_role_the_staff_acted_in(): void
    {
        $staff = $this->staff('courier', ['courier', 'warehouse']);
        $invoice = $this->invoice(['detail_status' => 2]);
        Sanctum::actingAs($staff);

        $this->postJson("/api/staff/invoices/{$invoice->id}/receive", [], [self::HEADER => 'warehouse'])
            ->assertOk();

        $event = InvoiceEvent::where('invoice_id', $invoice->id)->where('event', 'warehouse_receive')->firstOrFail();

        $this->assertSame('warehouse', $event->actor_role);
    }

    public function test_profile_returns_the_role_set(): void
    {
        $staff = $this->staff('warehouse', ['warehouse', 'agent']);
        Sanctum::actingAs($staff);

        $this->getJson('/api/staff/profile')
            ->assertOk()
            ->assertJsonPath('user.roles', ['warehouse', 'agent']);
    }
}
