<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Staff extends Authenticatable
{
    use HasApiTokens;

    /** Все роли сотрудников и их названия для интерфейса. */
    public const ROLE_LABELS = [
        'dispatcher' => 'Диспетчер',
        'courier'    => 'Курьер',
        'agent'      => 'Агент',
        'warehouse'  => 'Кладовщик',
    ];

    /**
     * Роли, которые работают с грузом в поле: забирают у отправителя,
     * принимают в пункте назначения и вручают получателю.
     * Агент — тот же курьер, но на принимающей стороне.
     */
    public const COURIER_ROLES = ['courier', 'agent'];

    protected $table = 'staff';

    protected $fillable = [
        'full_name',
        'login',
        'password',
        'role',
        'phone',
        'email',
        'note',
        'warehouse_location',
        'active',
    ];

    protected $hidden = [
        'password',
    ];

    /** Работает ли сотрудник с грузом в поле (курьер или агент). */
    public function isCourierRole(): bool
    {
        return self::isCourierRoleName($this->role);
    }

    /** То же самое, когда на руках только строка роли (например, из сессии). */
    public static function isCourierRoleName(?string $role): bool
    {
        return in_array($role, self::COURIER_ROLES, true);
    }

    /** Название роли для интерфейса. */
    public function roleLabel(): string
    {
        return self::ROLE_LABELS[$this->role] ?? $this->role;
    }
}
