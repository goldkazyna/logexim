<?php

namespace Tests\Feature;

use App\Models\CityDelivery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/** Калькулятор на главной — правила из КП LogExim Express (2026-10). */
class DeliveryCalculatorTest extends TestCase
{
    use RefreshDatabase;

    private int $almaty;
    private int $astana;
    private int $aktau;

    protected function setUp(): void
    {
        parent::setUp();
        $this->almaty = CityDelivery::create(['title' => 'Алматы'])->id;
        $this->astana = CityDelivery::create(['title' => 'Астана'])->id;
        $this->aktau = CityDelivery::create(['title' => 'Актау'])->id;
        DB::table('avto')->insert(['city_from' => $this->almaty, 'city_to' => $this->astana, 'price' => 120, 'time' => '2 дня']);
        DB::table('zh')->insert(['city_from' => $this->almaty, 'city_to' => $this->aktau, 'price' => 120, 'time' => '7-15 дней']);
        DB::table('avia')->insert(['city_from' => $this->almaty, 'city_to' => $this->astana, 'price' => 16400, 'time' => '1-2 дня']);
    }

    private function calc(array $params): \Illuminate\Testing\TestResponse
    {
        return $this->postJson('/ajax/calcDelivery', $params + [
            'package_from' => $this->almaty, 'package_to' => $this->astana, 'transport' => 'car',
        ])->assertOk();
    }

    public function test_up_to_20_kg_is_the_minimum_charge(): void
    {
        $this->calc(['weight' => 5])
            ->assertJson(['found' => true, 'price' => 9800, 'time' => '2 дня', 'chargeable_weight' => 5]);
        $this->calc(['weight' => 20])->assertJson(['price' => 9800]);
    }

    public function test_each_kg_over_20_is_charged_by_route_rate(): void
    {
        // 9800 + (50 - 20) × 120
        $this->calc(['weight' => 50])->assertJson(['price' => 13400, 'rate' => 120]);
    }

    public function test_volume_weight_wins_when_bigger(): void
    {
        // 100×100×50 / 5000 = 100 кг > 30 кг факт → 9800 + 80 × 120
        $this->calc(['weight' => 30, 'length' => 100, 'width' => 100, 'height' => 50])
            ->assertJson(['volume_weight' => 100, 'chargeable_weight' => 100, 'price' => 19400]);
    }

    public function test_oversize_and_non_stackable_coefficients(): void
    {
        // длина 310 см > 3 м → ×1,3; объёмный 310×50×50/5000 = 155 кг
        $this->calc(['weight' => 10, 'length' => 310, 'width' => 50, 'height' => 50])
            ->assertJson(['coefficient' => 1.3, 'price' => (int) round((9800 + 135 * 120) * 1.3)]);

        // вес > 500 кг → ×1,3; нештабелируемый → ×2 (берём больший)
        $this->calc(['weight' => 600, 'non_stackable' => 1])
            ->assertJson(['coefficient' => 2, 'price' => (9800 + 580 * 120) * 2]);
    }

    public function test_railway_uses_its_own_table(): void
    {
        $this->calc(['transport' => 'railway', 'package_to' => $this->aktau, 'weight' => 40])
            ->assertJson(['found' => true, 'price' => 9800 + 20 * 120, 'time' => '7-15 дней']);
    }

    public function test_unknown_route_asks_to_call_instead_of_inventing_a_price(): void
    {
        $this->calc(['package_to' => $this->aktau, 'weight' => 10])
            ->assertJson(['found' => false])
            ->assertJsonMissing(['price' => 8500]);
    }

    public function test_air_keeps_the_old_rules(): void
    {
        // цена направления за первые 3 кг + 850 ₸/кг сверху
        $this->calc(['transport' => 'air', 'weight' => 5])->assertJson(['price' => 16400 + 2 * 850]);
    }

    public function test_home_page_renders_new_calculator(): void
    {
        $this->assertStringContainsString('9 800', file_get_contents(resource_path('views/home.blade.php')));
        $this->assertStringNotContainsString('calcDeliveryCar', file_get_contents(resource_path('views/home.blade.php')));
    }
}
