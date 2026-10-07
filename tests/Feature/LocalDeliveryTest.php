<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
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

    public function test_same_city_flag_makes_delivery_local_even_if_names_differ(): void
    {
        $c = $this->courier();
        $inv = $this->invoice('г. Алматы', 'Алматы');
        $inv->update(['same_city' => true]);
        Sanctum::actingAs($c);

        $this->postJson("/api/staff/invoices/{$inv->id}/pickup", ['signature' => $this->sig()], ['X-Staff-Role' => 'courier'])->assertOk();

        $this->assertSame(5, (int) $inv->refresh()->detail_status, 'галочка «тот же город» — без склада');
    }

    public function test_cabinet_same_city_checkbox_copies_sender_city(): void
    {
        Http::fake();
        User::create([
            'bin' => '111122223333', 'password' => sha1(md5('x')),
            'company_name' => 'ТОО', 'director_name' => 'И', 'phone' => '+7', 'email' => 'c@example.com',
            'address' => '', 'city' => '', 'region' => '', 'country' => '', 'district' => '',
            'activate' => 1, 'activate_code' => '', 'restore_code' => '', 'date' => now(), 'ip' => '127.0.0.1',
        ]);

        $this->withSession(['bin' => '111122223333'])
            ->post('/cabinet/save_invoices', [
                'date' => '2026-10-07',
                'sender_name' => 'О', 'sender_phone' => '+7', 'sender_city' => 'Шымкент', 'sender_address' => 'a',
                'recipient_name' => 'П', 'recipient_phone' => '+7', 'recipient_city' => 'что-то другое', 'recipient_address' => 'b',
                'description' => 'x', 'quantity' => 1, 'weight' => 1,
                'same_city' => '1',
            ])->assertRedirect('/cabinet/invoices');

        $inv = Invoice::latest('id')->first();
        $this->assertSame('Шымкент', $inv->recipient_city);
        $this->assertTrue((bool) $inv->same_city);
        $this->assertTrue($inv->isLocalDelivery());
    }
}
