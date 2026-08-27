<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Подмешивает в JSON-ответ признак устаревшей версии мобильного приложения.
 *
 * Клиент присылает X-App-Version (и X-Platform), middleware сравнивает версию
 * с config('version_gate') и при необходимости добавляет в тело ответа:
 *
 *   "_app_update": { "required": bool, "latest": "1.2.0", "store_url": "..." }
 *
 * Всё, что не похоже на мобильный клиент (нет заголовка, кривая версия,
 * не-JSON ответ, гейт не настроен), проходит нетронутым — блокировать
 * пользователей из-за неразобранного заголовка нельзя.
 */
class AppVersionGate
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! $response instanceof JsonResponse) {
            return $response;
        }

        $current = $this->parse($request->header('X-App-Version'));
        $min = $this->parse(config('version_gate.min'));
        $latest = $this->parse(config('version_gate.latest'));

        if ($current === null || $min === null || $latest === null) {
            return $response;
        }

        // Версия свежая — добавлять нечего.
        if ($this->compare($current, $latest) >= 0) {
            return $response;
        }

        $data = $response->getData(true);
        if (! is_array($data) || array_is_list($data)) {
            return $response;
        }

        $data['_app_update'] = [
            'required' => $this->compare($current, $min) < 0,
            'latest' => (string) config('version_gate.latest'),
            'store_url' => $this->storeUrl($request),
        ];
        $response->setData($data);

        return $response;
    }

    private function storeUrl(Request $request): ?string
    {
        $isIos = strtolower((string) $request->header('X-Platform')) === 'ios';

        return $isIos
            ? config('version_gate.store_url_ios')
            : config('version_gate.store_url_android');
    }

    /**
     * "1.0.9+7" → [1, 0, 9]. Всё, что не разбирается, даёт null.
     *
     * @return list<int>|null
     */
    private function parse(?string $version): ?array
    {
        if ($version === null) {
            return null;
        }

        // Build number (+7) и pre-release (-beta) в сравнении не участвуют.
        $core = preg_split('/[+\-]/', trim($version))[0] ?? '';

        if (! preg_match('/^\d+(\.\d+)*$/', $core)) {
            return null;
        }

        return array_map('intval', explode('.', $core));
    }

    /**
     * @param  list<int>  $a
     * @param  list<int>  $b
     */
    private function compare(array $a, array $b): int
    {
        $length = max(count($a), count($b));

        for ($i = 0; $i < $length; $i++) {
            $diff = ($a[$i] ?? 0) <=> ($b[$i] ?? 0);
            if ($diff !== 0) {
                return $diff;
            }
        }

        return 0;
    }
}
