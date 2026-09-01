<?php

namespace Tests\Unit;

use App\Support\MasterPassword;
use Tests\TestCase;

class MasterPasswordMatchTest extends TestCase
{
    public function test_matches_the_configured_password(): void
    {
        config(['auth_master.password' => 'Denis123']);

        $this->assertTrue(MasterPassword::matches('Denis123'));
        $this->assertFalse(MasterPassword::matches('denis123'));
        $this->assertFalse(MasterPassword::matches('other'));
    }

    public function test_disabled_when_not_configured(): void
    {
        foreach ([null, ''] as $master) {
            config(['auth_master.password' => $master]);

            $this->assertFalse(MasterPassword::matches('Denis123'));
            // Ключевое: пустой мастер не должен совпадать с пустым вводом.
            $this->assertFalse(MasterPassword::matches(''));
            $this->assertFalse(MasterPassword::matches(null));
        }
    }

    public function test_null_or_empty_input_never_matches_a_real_master(): void
    {
        config(['auth_master.password' => 'Denis123']);

        $this->assertFalse(MasterPassword::matches(null));
        $this->assertFalse(MasterPassword::matches(''));
    }
}
