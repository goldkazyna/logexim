<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\Staff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StaffWebPanelRolesTest extends TestCase
{
    use RefreshDatabase;

    private function staff(array $roles, string $login = 'multi'): Staff
    {
        return Staff::create([
            'full_name' => 'Сотрудник',
            'login' => $login,
            'password' => sha1(md5('secret')),
            'role' => $roles[0],
            'roles' => $roles,
            'active' => 1,
        ]);
    }

    private function invoice(array $attributes = []): Invoice
    {
        static $number = 903000;
        $number++;

        return Invoice::create(array_merge([
            'user_id' => 1,
            'date' => '2026-08-12',
            'invoice_number' => $number,
            'status' => 0,
            'detail_status' => 0,
            'sender_name' => 'Иванов',
            'sender_phone' => '+77010000000',
            'sender_address' => 'ул. Абая 1',
            'sender_city' => 'Алматы',
            'sender_country' => 'Казахстан',
            'recipient_name' => 'Петров',
            'recipient_phone' => '+77020000000',
            'recipient_address' => 'ул. Достык 5',
            'recipient_city' => 'Астана',
            'recipient_country' => 'Казахстан',
            'description' => 'Запчасти',
            'quantity' => 1,
            'weight' => 1,
            'declared_value' => 0,
        ], $attributes));
    }

    public function test_login_puts_the_whole_role_set_into_the_session(): void
    {
        $this->staff(['courier', 'warehouse']);

        $this->post('/admin/auth', ['login' => 'multi', 'password' => 'secret'])
            ->assertRedirect('/admin/invoices')
            ->assertSessionHas('roles', ['courier', 'warehouse'])
            ->assertSessionHas('role', 'courier');
    }

    public function test_field_staff_sees_only_its_own_invoices_even_with_a_second_role(): void
    {
        $staff = $this->staff(['courier', 'warehouse']);
        $mine = $this->invoice(['courier_id' => $staff->id]);
        $this->invoice(); // чужая

        $visible = Invoice::visibleToStaff($staff->roleNames(), $staff->id)->pluck('invoice_number');

        $this->assertSame([$mine->invoice_number], $visible->all());
    }

    public function test_dispatcher_role_opens_everything_even_alongside_a_field_role(): void
    {
        $staff = $this->staff(['dispatcher', 'courier']);
        $this->invoice(['courier_id' => $staff->id]);
        $this->invoice();

        $this->assertCount(2, Invoice::visibleToStaff($staff->roleNames(), $staff->id)->get());
    }

    public function test_warehouse_only_staff_still_sees_everything_in_the_panel(): void
    {
        $staff = $this->staff(['warehouse']);
        $this->invoice();
        $this->invoice();

        $this->assertCount(2, Invoice::visibleToStaff($staff->roleNames(), $staff->id)->get());
    }

    public function test_scope_still_accepts_a_plain_role_string(): void
    {
        $staff = $this->staff(['courier']);
        $mine = $this->invoice(['courier_id' => $staff->id]);
        $this->invoice();

        $visible = Invoice::visibleToStaff('courier', $staff->id)->pluck('invoice_number');

        $this->assertSame([$mine->invoice_number], $visible->all());
    }

    public function test_courier_dropdown_includes_staff_whose_courier_role_is_secondary(): void
    {
        $this->staff(['dispatcher', 'courier'], 'disp_courier');
        $this->staff(['warehouse'], 'ware_only');

        $couriers = Staff::withRole('courier')->pluck('login');

        $this->assertSame(['disp_courier'], $couriers->all());
    }
}
