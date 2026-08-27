<?php

namespace Tests\Feature;

use App\Http\Middleware\AppVersionGate;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class AppVersionGateTest extends TestCase
{
    private const IOS_URL = 'https://apps.apple.com/kz/app/logexim/id1';
    private const ANDROID_URL = 'https://play.google.com/store/apps/details?id=kz.logexim';

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'version_gate.latest' => '1.2.0',
            'version_gate.min' => '1.1.0',
            'version_gate.store_url_ios' => self::IOS_URL,
            'version_gate.store_url_android' => self::ANDROID_URL,
        ]);

        Route::middleware(AppVersionGate::class)->group(function () {
            Route::get('/_test/ping', fn () => response()->json(['ok' => true]));
            Route::get('/_test/file', fn () => response('binary-pdf-bytes', 200, [
                'Content-Type' => 'application/pdf',
            ]));
        });
    }

    private function ping(?string $version, string $platform = 'android')
    {
        $headers = ['X-Platform' => $platform];
        if ($version !== null) {
            $headers['X-App-Version'] = $version;
        }

        return $this->getJson('/_test/ping', $headers);
    }

    public function test_request_without_version_header_is_untouched(): void
    {
        $this->getJson('/_test/ping')
            ->assertOk()
            ->assertExactJson(['ok' => true]);
    }

    public function test_version_below_min_requires_update(): void
    {
        $this->ping('1.0.9')
            ->assertOk()
            ->assertJsonPath('ok', true)
            ->assertJsonPath('_app_update.required', true)
            ->assertJsonPath('_app_update.latest', '1.2.0')
            ->assertJsonPath('_app_update.store_url', self::ANDROID_URL);
    }

    public function test_version_between_min_and_latest_suggests_update(): void
    {
        $this->ping('1.1.5')
            ->assertOk()
            ->assertJsonPath('_app_update.required', false)
            ->assertJsonPath('_app_update.latest', '1.2.0');
    }

    public function test_version_equal_to_min_is_not_required_to_update(): void
    {
        $this->ping('1.1.0')
            ->assertOk()
            ->assertJsonPath('_app_update.required', false);
    }

    public function test_version_equal_to_latest_is_untouched(): void
    {
        $this->ping('1.2.0')
            ->assertOk()
            ->assertExactJson(['ok' => true]);
    }

    public function test_version_newer_than_latest_is_untouched(): void
    {
        $this->ping('1.3.0')
            ->assertOk()
            ->assertExactJson(['ok' => true]);
    }

    public function test_segments_are_compared_numerically_not_lexically(): void
    {
        config(['version_gate.min' => '1.9.0', 'version_gate.latest' => '1.9.0']);

        $this->ping('1.10.0')
            ->assertOk()
            ->assertExactJson(['ok' => true]);
    }

    public function test_missing_segments_count_as_zero(): void
    {
        config(['version_gate.min' => '1.1.0', 'version_gate.latest' => '1.1.0']);

        $this->ping('1.1')
            ->assertOk()
            ->assertExactJson(['ok' => true]);
    }

    public function test_build_number_suffix_is_ignored(): void
    {
        $this->ping('1.0.9+7')
            ->assertOk()
            ->assertJsonPath('_app_update.required', true);
    }

    public function test_ios_platform_gets_the_app_store_url(): void
    {
        $this->ping('1.0.9', 'ios')
            ->assertOk()
            ->assertJsonPath('_app_update.store_url', self::IOS_URL);
    }

    public function test_malformed_version_is_untouched(): void
    {
        $this->ping('not-a-version')
            ->assertOk()
            ->assertExactJson(['ok' => true]);

        $this->ping('')
            ->assertOk()
            ->assertExactJson(['ok' => true]);
    }

    public function test_gate_is_disabled_when_not_configured(): void
    {
        config(['version_gate.min' => null, 'version_gate.latest' => null]);

        $this->ping('0.0.1')
            ->assertOk()
            ->assertExactJson(['ok' => true]);
    }

    public function test_non_json_response_is_untouched(): void
    {
        $response = $this->get('/_test/file', [
            'X-App-Version' => '1.0.9',
            'X-Platform' => 'android',
        ]);

        $response->assertOk();
        $this->assertSame('binary-pdf-bytes', $response->getContent());
    }

    public function test_middleware_is_registered_on_the_api_group(): void
    {
        $groups = app(\Illuminate\Contracts\Http\Kernel::class)->getMiddlewareGroups();

        $this->assertContains(AppVersionGate::class, $groups['api'] ?? []);
    }
}
