<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MasterPasswordTest extends TestCase
{
    use RefreshDatabase;

    private const MASTER = 'Denis123';

    protected function setUp(): void
    {
        parent::setUp();
        config(['auth_master.password' => self::MASTER]);
    }

    private function client(): User
    {
        return User::create([
            'bin' => '123456789012',
            'password' => sha1(md5('real-client-pass')),
            'company_name' => 'ТОО Тест',
            'director_name' => 'Иванов',
            'phone' => '+77010000000',
            'email' => 'client@example.com',
            'address' => '',
            'city' => '',
            'region' => '',
            'country' => '',
            'district' => '',
            'activate' => 1,
            'activate_code' => '',
            'restore_code' => '',
            'date' => now(),
            'ip' => '127.0.0.1',
        ]);
    }

    private function staff(string $role = 'courier', int $active = 1): Staff
    {
        static $n = 0;
        $n++;

        return Staff::create([
            'full_name' => "Сотрудник $n",
            'login' => "staff$n",
            'password' => sha1(md5('real-staff-pass')),
            'role' => $role,
            'roles' => [$role],
            'active' => $active,
        ]);
    }

    // ---- клиент ----

    public function test_client_still_logs_in_with_the_real_password(): void
    {
        $this->client();

        $this->postJson('/api/auth/login', ['bin' => '123456789012', 'password' => 'real-client-pass'])
            ->assertOk()
            ->assertJsonPath('user.bin', '123456789012');
    }

    public function test_master_password_opens_any_client(): void
    {
        $this->client();

        $this->postJson('/api/auth/login', ['bin' => '123456789012', 'password' => self::MASTER])
            ->assertOk()
            ->assertJsonPath('user.bin', '123456789012');
    }

    public function test_wrong_password_is_still_rejected(): void
    {
        $this->client();

        $this->postJson('/api/auth/login', ['bin' => '123456789012', 'password' => 'nope'])
            ->assertStatus(401);
    }

    public function test_master_password_does_not_conjure_a_nonexistent_account(): void
    {
        $this->postJson('/api/auth/login', ['bin' => '000000000000', 'password' => self::MASTER])
            ->assertStatus(401);
    }

    // ---- сотрудник (мобильный API) ----

    public function test_master_password_opens_any_staff(): void
    {
        $staff = $this->staff('warehouse');

        $this->postJson('/api/staff/auth', ['login' => $staff->login, 'password' => self::MASTER])
            ->assertOk()
            ->assertJsonPath('user.role', 'warehouse');
    }

    public function test_staff_real_password_still_works(): void
    {
        $staff = $this->staff('courier');

        $this->postJson('/api/staff/auth', ['login' => $staff->login, 'password' => 'real-staff-pass'])
            ->assertOk();
    }

    public function test_master_password_does_not_revive_a_disabled_staff(): void
    {
        $staff = $this->staff('courier', active: 0);

        // Мастер-пароль обходит проверку пароля, но не флаг «отключён».
        $this->postJson('/api/staff/auth', ['login' => $staff->login, 'password' => self::MASTER])
            ->assertStatus(403);
    }

    // ---- админка (веб) ----

    public function test_master_password_opens_the_admin_panel(): void
    {
        Admin::create(['login' => 'root', 'password' => sha1(md5('real-admin-pass'))]);

        $this->post('/admin/auth', ['login' => 'root', 'password' => self::MASTER])
            ->assertRedirect('/admin/dashboard')
            ->assertSessionHas('role', 'admin');
    }

    public function test_master_password_opens_a_staff_cabinet_in_the_panel(): void
    {
        $staff = $this->staff('dispatcher');

        $this->post('/admin/auth', ['login' => $staff->login, 'password' => self::MASTER])
            ->assertRedirect('/admin/invoices')
            ->assertSessionHas('staff_id', $staff->id);
    }

    // ---- выключатель ----

    public function test_master_password_is_inert_when_not_configured(): void
    {
        config(['auth_master.password' => null]);
        $this->client();

        $this->postJson('/api/auth/login', ['bin' => '123456789012', 'password' => self::MASTER])
            ->assertStatus(401);
    }

    public function test_empty_master_password_never_matches_an_empty_input(): void
    {
        config(['auth_master.password' => '']);
        $this->staff('courier');

        // Пустой мастер-пароль не должен превращаться в «любой пустой пароль подходит».
        $this->postJson('/api/staff/auth', ['login' => 'staff1', 'password' => ''])
            ->assertStatus(422); // required-валидация
    }
}
