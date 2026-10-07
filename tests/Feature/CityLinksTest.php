<?php

namespace Tests\Feature;

use App\Models\CityDelivery;
use App\Models\Invoice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
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

    public function test_city_links_and_same_names_no_longer_make_delivery_local(): void
    {
        $alm = $this->city('Алматы');
        $shy = $this->city('Шымкент');
        $this->link($alm->id, $shy->id);

        // Теперь без склада — только по галочке «тот же город».
        $this->assertFalse($this->invoice('Алматы', 'Шымкент')->isLocalDelivery());
        $this->assertFalse($this->invoice('Актобе', 'Актобе')->isLocalDelivery());
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
    }

    public function test_cities_page_opens_with_selected_city(): void
    {
        $this->withSession(['admin' => 'admin', 'role' => 'admin', 'roles' => ['admin']]);
        $alm = $this->city('Алматы');
        $this->city('Шымкент');

        $this->get('/admin/cities?city=' . $alm->id)
            ->assertOk()
            ->assertSee('Шымкент')
            ->assertSee('linked[]', false);
    }
}
