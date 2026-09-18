<?php

namespace Tests\Feature;

use App\Models\CityDelivery;
use App\Models\Invoice;
use App\Models\Staff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ZoneLocalDeliveryTest extends TestCase
{
    use RefreshDatabase;

    private function city(string $title, ?string $zone = null): void
    {
        CityDelivery::create(['title' => $title, 'zone' => $zone]);
    }

    private function invoice(string $from, string $to): Invoice
    {
        static $n = 903000;
        $n++;

        return Invoice::create([
            'user_id' => 1, 'date' => '2026-09-18', 'invoice_number' => $n,
            'status' => 0, 'detail_status' => 0,
            'sender_name' => 'О', 'sender_phone' => '+7', 'sender_address' => 'a',
            'sender_city' => $from, 'sender_country' => 'KZ',
            'recipient_name' => 'П', 'recipient_phone' => '+7', 'recipient_address' => 'b',
            'recipient_city' => $to, 'recipient_country' => 'KZ',
            'description' => 'x', 'quantity' => 1, 'weight' => 5, 'declared_value' => 0,
        ]);
    }

    public function test_same_zone_is_local(): void
    {
        $this->city('Алматы', 'Алматы');
        $this->city('Талгар', 'Алматы');

        $this->assertTrue($this->invoice('Алматы', 'Талгар')->isLocalDelivery());
        $this->assertTrue($this->invoice('Талгар', 'Алматы')->isLocalDelivery(), 'симметрично');
    }

    public function test_different_zones_are_not_local(): void
    {
        $this->city('Алматы', 'Алматы');
        $this->city('Астана', 'Астана');

        $this->assertFalse($this->invoice('Алматы', 'Астана')->isLocalDelivery());
    }

    public function test_same_city_still_local_without_zone(): void
    {
        $this->city('Костанай', null);

        $this->assertTrue($this->invoice('Костанай', 'Костанай')->isLocalDelivery());
    }

    public function test_no_zone_different_cities_not_local(): void
    {
        $this->city('Актобе', null);
        $this->city('Атырау', null);

        $this->assertFalse($this->invoice('Актобе', 'Атырау')->isLocalDelivery());
    }

    public function test_pickup_of_same_zone_skips_warehouse(): void
    {
        $this->city('Алматы', 'Алматы');
        $this->city('Есик', 'Алматы');
        $courier = Staff::create([
            'full_name' => 'К', 'login' => 'k1', 'password' => sha1(md5('x')),
            'role' => 'courier', 'roles' => ['courier'], 'active' => 1,
        ]);
        $inv = $this->invoice('Алматы', 'Есик');
        Sanctum::actingAs($courier);

        $sig = 'data:image/png;base64,' . base64_encode(str_repeat('sig', 8));
        $this->postJson("/api/staff/invoices/{$inv->id}/pickup", ['signature' => $sig], ['X-Staff-Role' => 'courier'])
            ->assertOk();

        $inv->refresh();
        $this->assertSame(5, (int) $inv->detail_status, 'одна зона — минуем склад');
        $this->assertSame($courier->id, (int) $inv->receiving_courier_id);
    }
}
