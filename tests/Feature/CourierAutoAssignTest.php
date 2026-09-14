<?php

namespace Tests\Feature;

use App\Models\CityDelivery;
use App\Models\Invoice;
use App\Models\InvoiceEvent;
use App\Models\Staff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourierAutoAssignTest extends TestCase
{
    use RefreshDatabase;

    private function city(string $title): CityDelivery
    {
        return CityDelivery::firstOrCreate(['title' => $title]);
    }

    private function staff(string $role, string $login, array $cityTitles): Staff
    {
        $s = Staff::create([
            'full_name' => "Сотрудник $login", 'login' => $login, 'password' => sha1(md5('x')),
            'role' => $role, 'roles' => [$role], 'active' => 1,
        ]);
        $s->cities()->sync(collect($cityTitles)->map(fn ($t) => $this->city($t)->id)->all());

        return $s;
    }

    private function make(array $attributes = []): Invoice
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

    public function test_assigns_pickup_and_destination_couriers_by_city(): void
    {
        $pickup = $this->staff('courier', 'alm_courier', ['Алматы']);
        $dest = $this->staff('agent', 'ast_agent', ['Астана']);

        $inv = $this->make(['sender_city' => 'Алматы', 'recipient_city' => 'Астана']);

        $this->assertSame($pickup->id, (int) $inv->refresh()->courier_id);
        $this->assertSame($dest->id, (int) $inv->receiving_courier_id);
    }

    public function test_pickup_assignment_advances_stage_to_courier_assigned(): void
    {
        $this->staff('courier', 'alm_courier', ['Алматы']);

        $inv = $this->make(['sender_city' => 'Алматы', 'recipient_city' => 'Костанай']);

        $this->assertSame(1, (int) $inv->refresh()->detail_status);
    }

    public function test_no_courier_for_city_leaves_it_unassigned(): void
    {
        $this->staff('courier', 'alm_courier', ['Алматы']);

        $inv = $this->make(['sender_city' => 'Шымкент', 'recipient_city' => 'Костанай']);
        $inv->refresh();

        $this->assertNull($inv->courier_id);
        $this->assertNull($inv->receiving_courier_id);
        $this->assertSame(0, (int) $inv->detail_status);
    }

    public function test_inactive_courier_is_skipped(): void
    {
        $s = $this->staff('courier', 'off_courier', ['Алматы']);
        $s->update(['active' => 0]);

        $inv = $this->make(['sender_city' => 'Алматы']);

        $this->assertNull($inv->refresh()->courier_id);
    }

    public function test_prefers_courier_role_for_pickup_and_agent_for_destination(): void
    {
        // В городе и курьер, и агент — на отправку берём курьера, в пункт — агента.
        $courier = $this->staff('courier', 'c_alm', ['Алматы']);
        $agent = $this->staff('agent', 'a_alm', ['Алматы']);

        $inv = $this->make(['sender_city' => 'Алматы', 'recipient_city' => 'Алматы']);
        $inv->refresh();

        $this->assertSame($courier->id, (int) $inv->courier_id);
        $this->assertSame($agent->id, (int) $inv->receiving_courier_id);
    }

    public function test_does_not_overwrite_preassigned_courier(): void
    {
        $auto = $this->staff('courier', 'alm_courier', ['Алматы']);

        $inv = $this->make(['sender_city' => 'Алматы', 'courier_id' => 555]);

        $this->assertSame(555, (int) $inv->refresh()->courier_id);
    }

    public function test_logs_assignment_events(): void
    {
        $this->staff('courier', 'alm_courier', ['Алматы']);
        $inv = $this->make(['sender_city' => 'Алматы', 'recipient_city' => 'Костанай']);

        $this->assertDatabaseHas('invoice_events', [
            'invoice_id' => $inv->id, 'event' => 'courier_assigned', 'actor_type' => 'system',
        ]);
    }

    public function test_unknown_city_title_assigns_nothing(): void
    {
        $this->staff('courier', 'alm_courier', ['Алматы']);
        $inv = $this->make(['sender_city' => 'Городок-которого-нет']);

        $this->assertNull($inv->refresh()->courier_id);
    }
}
