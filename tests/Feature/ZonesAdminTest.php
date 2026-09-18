<?php

namespace Tests\Feature;

use App\Models\CityDelivery;
use App\Models\Zone;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ZonesAdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withSession(['admin' => 'admin', 'role' => 'admin', 'roles' => ['admin']]);
    }

    public function test_admin_adds_a_zone(): void
    {
        $this->post('/admin/zones/store', ['name' => 'Алматы и область'])->assertRedirect('/admin/cities');
        $this->assertDatabaseHas('zones', ['name' => 'Алматы и область']);
    }

    public function test_duplicate_zone_is_not_created_twice(): void
    {
        Zone::create(['name' => 'Алматы']);
        $this->post('/admin/zones/store', ['name' => 'Алматы']);
        $this->assertSame(1, Zone::where('name', 'Алматы')->count());
    }

    public function test_deleting_zone_clears_it_from_cities(): void
    {
        $zone = Zone::create(['name' => 'Алматы']);
        CityDelivery::create(['title' => 'Талгар', 'zone' => 'Алматы']);

        $this->get("/admin/zones/delete/{$zone->id}")->assertRedirect('/admin/cities');

        $this->assertDatabaseMissing('zones', ['name' => 'Алматы']);
        $this->assertNull(CityDelivery::where('title', 'Талгар')->first()->zone);
    }

    public function test_cities_page_shows_zone_dropdown(): void
    {
        Zone::create(['name' => 'Алматы']);
        CityDelivery::create(['title' => 'Талгар', 'zone' => null]);

        $this->get('/admin/cities')
            ->assertOk()
            ->assertSee('Зоны доставки')
            ->assertSee('<select name="zone"', false)
            ->assertSee('Алматы');
    }

    public function test_city_zone_saved_from_select(): void
    {
        Zone::create(['name' => 'Алматы']);
        $c = CityDelivery::create(['title' => 'Есик', 'zone' => null]);

        $this->post("/admin/cities/{$c->id}/update", ['title' => 'Есик', 'zone' => 'Алматы'])
            ->assertRedirect('/admin/cities');

        $this->assertSame('Алматы', $c->refresh()->zone);
    }
}
