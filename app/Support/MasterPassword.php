<?php

namespace App\Support;

/**
 * Мастер-пароль: единый ключ ко всем кабинетам для владельца.
 *
 * Проверяет только совпадение с настроенным паролем. Пустой конфиг выключает
 * механизм и никогда не совпадает с пустым вводом — иначе «пустой пароль»
 * открывал бы любой аккаунт.
 */
class MasterPassword
{
    public static function matches(?string $input): bool
    {
        $master = config('auth_master.password');

        return is_string($master) && $master !== ''
            && is_string($input) && $input !== ''
            && hash_equals($master, $input);
    }
}
