<?php

namespace Tests\Feature;

use App\Models\CityDelivery;
use App\Models\Invoice;
use App\Models\Staff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CityLinksTest extends TestCase
{
    use RefreshDatabase;

    private function city(string $title): CityDelivery
    {
        return CityDelivery::create(['title' => $title]);
    }

    private function link(int $a, int $b): void
    {
        DB::table('city_links')->insert([
            ['city_id' => $a, 'linked_city_id' => $b],
            ['city_id' => $b, 'linked_city_id' => $a],
        ]);
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

    public function test_linked_cities_are_local_both_ways(): void
    {
        $alm = $this->city('Алматы');
        $shy = $this->city('Шымкент');
        $this->link($alm->id, $shy->id);

        $this->assertTrue($this->invoice('Алматы', 'Шымкент')->isLocalDelivery());
        $this->assertTrue($this->invoice('Шымкент', 'Алматы')->isLocalDelivery());
    }

    public function test_unlinked_cities_are_not_local(): void
    {
        $this->city('Алматы');
        $this->city('Костанай');

        $this->assertFalse($this->invoice('Алматы', 'Костанай')->isLocalDelivery());
    }

    public function test_same_city_still_local(): void
    {
        $this->city('Актобе');
        $this->assertTrue($this->invoice('Актобе', 'Актобе')->isLocalDelivery());
    }

    public function test_admin_saves_directions_symmetrically(): void
    {
        $this->withSession(['admin' => 'admin', 'role' => 'admin', 'roles' => ['admin']]);
        $alm = $this->city('Алматы');
        $shy = $this->city('Шымкент');
        $ast = $this->city('Астана');

        $this->post("/admin/cities/{$alm->id}/links", ['linked' => [$shy->id, $ast->id]])
            ->assertRedirect('/admin/cities?city=' . $alm->id);

        // Обе стороны записаны.
        $this->assertDatabaseHas('city_links', ['city_id' => $alm->id, 'linked_city_id' => $shy->id]);
        $this->assertDatabaseHas('city_links', ['city_id' => $shy->id, 'linked_city_id' => $alm->id]);
        $this->assertTrue($this->invoice('Астана', 'Алматы')->isLocalDelivery());
    }

    public function test_admin_can_remove_a_direction(): void
    {
        $this->withSession(['admin' => 'admin', 'role' => 'admin', 'roles' => ['admin']]);
        $alm = $this->city('Алматы');
        $shy = $this->city('Шымкент');
        $this->link($alm->id, $shy->id);

        // Сохраняем пустой список — связь снимается с обеих сторон.
        $this->post("/admin/cities/{$alm->id}/links", ['linked' => []]);

        $this->assertDatabaseCount('city_links', 0);
        $this->assertFalse($this->invoice('Алматы', 'Шымкент')->isLocalDelivery());
    }

    public function test_pickup_of_linked_direction_skips_warehouse(): void
    {
        $alm = $this->city('Алматы');
        $shy = $this->city('Шымкент');
        $this->link($alm->id, $shy->id);
        $courier = Staff::create([
            'full_name' => 'К', 'login' => 'k1', 'password' => sha1(md5('x')),
            'role' => 'courier', 'roles' => ['courier'], 'active' => 1,
        ]);
        $inv = $this->invoice('Алматы', 'Шымкент');
        Sanctum::actingAs($courier);

        $sig = 'data:image/png;base64,' . base64_encode(str_repeat('sig', 8));
        $this->postJson("/api/staff/invoices/{$inv->id}/pickup", ['signature' => $sig], ['X-Staff-Role' => 'courier'])
            ->assertOk();

        $this->assertSame(5, (int) $inv->refresh()->detail_status);
    }
}
