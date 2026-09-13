<?php

namespace Tests\Feature;

use App\Models\CityDelivery;
use App\Models\Staff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StaffCitiesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withSession(['admin' => 'admin', 'role' => 'admin']);
    }

    /** @return array<int,int> */
    private function cities(): array
    {
        return [
            CityDelivery::create(['title' => 'Алматы'])->id,
            CityDelivery::create(['title' => 'Астана'])->id,
            CityDelivery::create(['title' => 'Шымкент'])->id,
        ];
    }

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'full_name' => 'Курьер Тестовый',
            'login' => 'courier_test',
            'password' => 'secret',
            'roles' => ['courier'],
            'phone' => '+77010000000',
            'email' => 'c@example.com',
            'active' => 1,
        ], $overrides);
    }

    public function test_admin_attaches_several_cities_when_creating_staff(): void
    {
        [$alm, $ast] = $this->cities();

        $this->post('/admin/staff', $this->payload(['city_ids' => [$alm, $ast]]))
            ->assertRedirect('/admin/staff');

        $staff = Staff::where('login', 'courier_test')->firstOrFail();
        $this->assertEqualsCanonicalizing([$alm, $ast], $staff->cities->pluck('id')->all());
    }

    public function test_staff_can_be_created_without_cities(): void
    {
        $this->cities();

        $this->post('/admin/staff', $this->payload())->assertRedirect('/admin/staff');

        $this->assertCount(0, Staff::where('login', 'courier_test')->firstOrFail()->cities);
    }

    public function test_editing_syncs_cities(): void
    {
        [$alm, $ast, $shy] = $this->cities();
        $staff = Staff::create([
            'full_name' => 'Курьер', 'login' => 'c1', 'password' => sha1(md5('x')),
            'role' => 'courier', 'roles' => ['courier'], 'active' => 1,
        ]);
        $staff->cities()->sync([$alm, $ast]);

        $this->post("/admin/staff/{$staff->id}", [
            'full_name' => 'Курьер', 'login' => 'c1', 'roles' => ['courier'],
            'active' => 1, 'city_ids' => [$shy],
        ])->assertRedirect('/admin/staff');

        $this->assertSame([$shy], $staff->refresh()->cities->pluck('id')->all());
    }

    public function test_clearing_all_cities_on_edit(): void
    {
        [$alm] = $this->cities();
        $staff = Staff::create([
            'full_name' => 'Курьер', 'login' => 'c2', 'password' => sha1(md5('x')),
            'role' => 'courier', 'roles' => ['courier'], 'active' => 1,
        ]);
        $staff->cities()->sync([$alm]);

        $this->post("/admin/staff/{$staff->id}", [
            'full_name' => 'Курьер', 'login' => 'c2', 'roles' => ['courier'], 'active' => 1,
        ])->assertRedirect('/admin/staff');

        $this->assertCount(0, $staff->refresh()->cities);
    }

    public function test_unknown_city_ids_are_ignored(): void
    {
        [$alm] = $this->cities();

        $this->post('/admin/staff', $this->payload(['city_ids' => [$alm, 999999]]))
            ->assertRedirect('/admin/staff');

        $this->assertSame([$alm], Staff::where('login', 'courier_test')->firstOrFail()->cities->pluck('id')->all());
    }

    public function test_cities_show_on_the_staff_list(): void
    {
        [$alm, $ast] = $this->cities();
        $staff = Staff::create([
            'full_name' => 'Курьер Городской', 'login' => 'c3', 'password' => sha1(md5('x')),
            'role' => 'courier', 'roles' => ['courier'], 'active' => 1,
        ]);
        $staff->cities()->sync([$alm, $ast]);

        $this->get('/admin/staff')
            ->assertOk()
            ->assertSee('Алматы')
            ->assertSee('Астана');
    }
}
