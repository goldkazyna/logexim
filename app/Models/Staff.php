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
        'roles',
        'phone',
        'email',
        'note',
        'warehouse_location',
        'active',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'roles' => 'array',
    ];

    /**
     * Все роли сотрудника. Колонка roles — источник истины; строковая role
     * осталась ради кода, написанного до многоролевости, и всегда равна
     * первой роли набора.
     *
     * @return list<string>
     */
    public function roleNames(): array
    {
        $roles = array_values(array_filter(
            (array) ($this->roles ?? []),
            fn ($role) => isset(self::ROLE_LABELS[$role]),
        ));

        if ($roles !== []) {
            return $roles;
        }

        return isset(self::ROLE_LABELS[$this->role]) ? [$this->role] : [];
    }

    /** Сотрудники, у которых есть такая роль — основная или дополнительная. */
    public function scopeWithRole($query, string $role)
    {
        return $query->where(function ($q) use ($role) {
            $q->whereJsonContains('roles', $role)
              ->orWhere(function ($q2) use ($role) {
                  $q2->where('role', $role)
                     ->where(fn ($q3) => $q3->whereNull('roles')->orWhere('roles', '[]'));
              });
        });
    }

    public function hasRole(?string $role): bool
    {
        return $role !== null && in_array($role, $this->roleNames(), true);
    }

    /** Роль по умолчанию — в ней сотрудник оказывается, пока не переключился. */
    public function primaryRole(): ?string
    {
        return $this->roleNames()[0] ?? null;
    }

    /**
     * В какой роли сотрудник работает прямо сейчас.
     *
     * Мобильное приложение присылает её в заголовке X-Staff-Role. Пустое
     * значение — основная роль (так ведут себя сборки, выпущенные до
     * многоролевости). Роль, которой у сотрудника нет, даёт null: вызывающий
     * обязан ответить 403, а не молча подставить другую.
     */
    public function resolveActiveRole(?string $requested): ?string
    {
        $requested = trim((string) $requested);

        if ($requested === '') {
            return $this->primaryRole();
        }

        return $this->hasRole($requested) ? $requested : null;
    }

    /** Работает ли сотрудник с грузом в поле (курьер или агент) хотя бы в одной из ролей. */
    public function isCourierRole(): bool
    {
        foreach ($this->roleNames() as $role) {
            if (self::isCourierRoleName($role)) {
                return true;
            }
        }

        return false;
    }

    /** То же самое, когда на руках только строка роли (например, из сессии). */
    public static function isCourierRoleName(?string $role): bool
    {
        return in_array($role, self::COURIER_ROLES, true);
    }

    /** Названия всех ролей через запятую — для интерфейса. */
    public function roleLabel(): string
    {
        $labels = array_map(fn ($role) => self::ROLE_LABELS[$role], $this->roleNames());

        return $labels === [] ? (string) $this->role : implode(', ', $labels);
    }
}
