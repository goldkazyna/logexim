<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\Staff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class LocalDeliveryTest extends TestCase
{
    use RefreshDatabase;

    private function courier(): Staff
    {
        static $n = 0;
        $n++;

        return Staff::create([
            'full_name' => "К $n", 'login' => "k$n", 'password' => sha1(md5('x')),
            'role' => 'courier', 'roles' => ['courier'], 'active' => 1,
        ]);
    }

    private function invoice(string $from, string $to): Invoice
    {
        static $n = 903000;
        $n++;

        return Invoice::create([
            'user_id' => 1, 'date' => '2026-09-14', 'invoice_number' => $n,
            'status' => 0, 'detail_status' => 0,
            'sender_name' => 'О', 'sender_phone' => '+7', 'sender_address' => 'a',
            'sender_city' => $from, 'sender_country' => 'KZ',
            'recipient_name' => 'П', 'recipient_phone' => '+7', 'recipient_address' => 'b',
            'recipient_city' => $to, 'recipient_country' => 'KZ',
            'description' => 'x', 'quantity' => 1, 'weight' => 5, 'declared_value' => 0,
        ]);
    }

    private function sig(): string
    {
        return 'data:image/png;base64,' . base64_encode(str_repeat('sig', 8));
    }

    public function test_same_city_pickup_skips_warehouse(): void
    {
        $c = $this->courier();
        $inv = $this->invoice('Алматы', 'Алматы');
        Sanctum::actingAs($c);

        $this->postJson("/api/staff/invoices/{$inv->id}/pickup", ['signature' => $this->sig()], ['X-Staff-Role' => 'courier'])
            ->assertOk();

        $inv->refresh();
        $this->assertSame(5, (int) $inv->detail_status, 'сразу к доставке, минуя склад');
        $this->assertSame($c->id, (int) $inv->courier_id);
        $this->assertSame($c->id, (int) $inv->receiving_courier_id, 'тот же курьер доставляет');
    }

    public function test_same_courier_delivers_local_invoice_directly(): void
    {
        $c = $this->courier();
        $inv = $this->invoice('Алматы', 'алматы '); // регистр/пробел не важны
        Sanctum::actingAs($c);

        $this->postJson("/api/staff/invoices/{$inv->id}/pickup", ['signature' => $this->sig()], ['X-Staff-Role' => 'courier'])->assertOk();

        // Доставка тем же курьером — без склада и без destination-pickup.
        $this->postJson("/api/staff/invoices/{$inv->id}/deliver", ['signature' => $this->sig()], ['X-Staff-Role' => 'courier'])
            ->assertOk();

        $inv->refresh();
        $this->assertSame(6, (int) $inv->detail_status);
        $this->assertSame(3, (int) $inv->status);
        $this->assertNotNull($inv->delivered_at);
    }

    public function test_different_city_still_goes_through_warehouse(): void
    {
        $c = $this->courier();
        $inv = $this->invoice('Алматы', 'Астана');
        Sanctum::actingAs($c);

        $this->postJson("/api/staff/invoices/{$inv->id}/pickup", ['signature' => $this->sig()], ['X-Staff-Role' => 'courier'])->assertOk();

        $inv->refresh();
        $this->assertSame(2, (int) $inv->detail_status, 'межгород — на склад');
        $this->assertNull($inv->receiving_courier_id);
    }
}
