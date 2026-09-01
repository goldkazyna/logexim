<?php

namespace Tests\Feature;

use App\Models\Staff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StaffAdminRolesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withSession(['admin' => 'admin', 'role' => 'admin']);
    }

    /** @return array<string, mixed> */
    private function payload(array $overrides = []): array
    {
        return array_merge([
            'full_name' => 'Иванов Иван',
            'login' => 'ivanov',
            'password' => 'secret',
            'roles' => ['courier'],
            'phone' => '+77010000000',
            'email' => 'ivanov@example.com',
            'active' => 1,
        ], $overrides);
    }

    private function existing(array $overrides = []): Staff
    {
        return Staff::create(array_merge([
            'full_name' => 'Петров Пётр',
            'login' => 'petrov',
            'password' => sha1(md5('secret')),
            'role' => 'courier',
            'roles' => ['courier'],
            'active' => 1,
        ], $overrides));
    }

    public function test_admin_creates_staff_with_several_roles(): void
    {
        $this->post('/admin/staff', $this->payload(['roles' => ['courier', 'warehouse']]))
            ->assertRedirect('/admin/staff');

        $staff = Staff::where('login', 'ivanov')->firstOrFail();

        $this->assertSame(['courier', 'warehouse'], $staff->roleNames());
        $this->assertSame('courier', $staff->role, 'основная роль — первая из набора');
        $this->assertTrue($staff->hasRole('warehouse'));
    }

    public function test_single_role_still_works(): void
    {
        $this->post('/admin/staff', $this->payload(['roles' => ['warehouse']]))
            ->assertRedirect('/admin/staff');

        $staff = Staff::where('login', 'ivanov')->firstOrFail();

        $this->assertSame(['warehouse'], $staff->roleNames());
        $this->assertSame('warehouse', $staff->role);
    }

    public function test_at_least_one_role_is_required(): void
    {
        $this->post('/admin/staff', $this->payload(['roles' => []]))
            ->assertSessionHasErrors('roles');

        $this->post('/admin/staff', array_diff_key($this->payload(), ['roles' => null]))
            ->assertSessionHasErrors('roles');

        $this->assertDatabaseCount('staff', 0);
    }

    public function test_unknown_role_is_rejected(): void
    {
        $this->post('/admin/staff', $this->payload(['roles' => ['courier', 'wizard']]))
            ->assertSessionHasErrors('roles.1');

        $this->assertDatabaseCount('staff', 0);
    }

    public function test_warehouse_location_is_kept_only_while_the_warehouse_role_is_present(): void
    {
        $this->post('/admin/staff', $this->payload([
            'roles' => ['courier', 'warehouse'],
            'warehouse_location' => 'Алматы — Центральный',
        ]));

        $this->assertSame('Алматы — Центральный', Staff::where('login', 'ivanov')->firstOrFail()->warehouse_location);
    }

    public function test_warehouse_location_is_dropped_without_the_warehouse_role(): void
    {
        $this->post('/admin/staff', $this->payload([
            'roles' => ['courier'],
            'warehouse_location' => 'Алматы — Центральный',
        ]));

        $this->assertNull(Staff::where('login', 'ivanov')->firstOrFail()->warehouse_location);
    }

    public function test_admin_adds_a_role_to_an_existing_courier(): void
    {
        $staff = $this->existing();

        $this->post("/admin/staff/{$staff->id}", [
            'full_name' => $staff->full_name,
            'login' => $staff->login,
            'roles' => ['courier', 'warehouse'],
            'warehouse_location' => 'Астана — Склад',
            'active' => 1,
        ])->assertRedirect('/admin/staff');

        $staff->refresh();

        $this->assertSame(['courier', 'warehouse'], $staff->roleNames());
        $this->assertSame('Астана — Склад', $staff->warehouse_location);
    }

    public function test_admin_removes_a_role(): void
    {
        $staff = $this->existing(['role' => 'courier', 'roles' => ['courier', 'warehouse']]);

        $this->post("/admin/staff/{$staff->id}", [
            'full_name' => $staff->full_name,
            'login' => $staff->login,
            'roles' => ['warehouse'],
            'active' => 1,
        ])->assertRedirect('/admin/staff');

        $staff->refresh();

        $this->assertSame(['warehouse'], $staff->roleNames());
        $this->assertSame('warehouse', $staff->role, 'основная роль пересчитывается по набору');
    }

    public function test_staff_list_shows_every_role(): void
    {
        $this->existing(['roles' => ['courier', 'warehouse']]);

        $this->get('/admin/staff')
            ->assertOk()
            ->assertSee('Курьер, Кладовщик');
    }
}
