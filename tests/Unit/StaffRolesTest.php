<?php

namespace Tests\Unit;

use App\Models\Staff;
use Tests\TestCase;

class StaffRolesTest extends TestCase
{
    private function staff(?string $role, ?array $roles = null): Staff
    {
        $staff = new Staff(['role' => $role]);
        $staff->roles = $roles;

        return $staff;
    }

    public function test_single_role_staff_reports_exactly_that_role(): void
    {
        $staff = $this->staff('courier');

        $this->assertSame(['courier'], $staff->roleNames());
        $this->assertTrue($staff->hasRole('courier'));
        $this->assertFalse($staff->hasRole('warehouse'));
    }

    public function test_roles_column_wins_when_filled(): void
    {
        $staff = $this->staff('courier', ['courier', 'warehouse']);

        $this->assertSame(['courier', 'warehouse'], $staff->roleNames());
        $this->assertTrue($staff->hasRole('courier'));
        $this->assertTrue($staff->hasRole('warehouse'));
    }

    public function test_empty_roles_column_falls_back_to_the_primary_role(): void
    {
        foreach ([null, []] as $empty) {
            $staff = $this->staff('warehouse', $empty);

            $this->assertSame(['warehouse'], $staff->roleNames());
        }
    }

    public function test_unknown_roles_are_ignored(): void
    {
        $staff = $this->staff('courier', ['courier', 'wizard', '']);

        $this->assertSame(['courier'], $staff->roleNames());
        $this->assertFalse($staff->hasRole('wizard'));
    }

    public function test_courier_role_check_covers_the_whole_set(): void
    {
        $this->assertTrue($this->staff('warehouse', ['warehouse', 'courier'])->isCourierRole());
        $this->assertTrue($this->staff('warehouse', ['warehouse', 'agent'])->isCourierRole());
        $this->assertFalse($this->staff('warehouse', ['warehouse', 'dispatcher'])->isCourierRole());
    }

    public function test_primary_role_is_the_first_of_the_set(): void
    {
        $this->assertSame('warehouse', $this->staff('courier', ['warehouse', 'courier'])->primaryRole());
        $this->assertSame('courier', $this->staff('courier')->primaryRole());
    }

    public function test_role_label_lists_every_role(): void
    {
        $this->assertSame('Курьер', $this->staff('courier')->roleLabel());
        $this->assertSame('Курьер, Кладовщик', $this->staff('courier', ['courier', 'warehouse'])->roleLabel());
    }

    public function test_active_role_defaults_to_the_primary_one(): void
    {
        $staff = $this->staff('courier', ['courier', 'warehouse']);

        $this->assertSame('courier', $staff->resolveActiveRole(null));
        $this->assertSame('courier', $staff->resolveActiveRole(''));
    }

    public function test_active_role_honours_a_role_the_staff_actually_has(): void
    {
        $staff = $this->staff('courier', ['courier', 'warehouse']);

        $this->assertSame('warehouse', $staff->resolveActiveRole('warehouse'));
        $this->assertSame('courier', $staff->resolveActiveRole('courier'));
    }

    public function test_active_role_rejects_a_role_the_staff_does_not_have(): void
    {
        $staff = $this->staff('courier', ['courier', 'warehouse']);

        $this->assertNull($staff->resolveActiveRole('dispatcher'));
        $this->assertNull($staff->resolveActiveRole('wizard'));
    }
}
