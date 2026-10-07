<?php

namespace App\Support;

use App\Models\Avia;
use App\Models\Avto;
use App\Models\Zh;

/**
 * Калькулятор стоимости доставки («Рассчитать стоимость» на главной).
 *
 * Авто и ж/д — по КП LogExim Express (2026-10):
 *  - тариф за 1 кг из справочника направлений (/admin/tariffs);
 *  - считается больший из весов: физический или объёмный = Д×Ш×В(см)/5000;
 *  - минимальный сбор 9 800 ₸ за первые 20 кг, каждый следующий кг — по тарифу;
 *  - негабарит (длина > 3 м, ширина > 2 м, высота > 1,8 м или вес > 500 кг) ×1,3,
 *    нештабелируемый груз ×2 (применяется больший коэффициент).
 * Авиа — по прежним правилам: цена направления за первые 3 кг, далее 850 ₸/кг.
 */
class DeliveryCalculator
{
    public const MIN_CHARGE = 9800;
    public const MIN_CHARGE_KG = 20;
    public const VOLUME_DIVISOR = 5000;

    public const OVERSIZE_COEF = 1.3;
    public const NON_STACKABLE_COEF = 2.0;
    public const OVERSIZE_LIMITS = ['length' => 300, 'width' => 200, 'height' => 180, 'weight' => 500];

    private const AIR_BASE_KG = 3;
    private const AIR_EXTRA_PER_KG = 850;
    private const AIR_VOLUME_DIVISOR = 6000;

    /**
     * @param  array{weight?: float, length?: float, width?: float, height?: float, non_stackable?: bool}  $cargo
     * @return array{found: bool, price?: int, rate?: float, time?: string, weight: float, volume_weight: float, chargeable_weight: float, coefficient?: float, notes?: list<string>}
     */
    public function calculate(string $transport, int $fromId, int $toId, array $cargo): array
    {
        $weight = max(0.0, (float) ($cargo['weight'] ?? 0));
        $l = max(0.0, (float) ($cargo['length'] ?? 0));
        $w = max(0.0, (float) ($cargo['width'] ?? 0));
        $h = max(0.0, (float) ($cargo['height'] ?? 0));

        $divisor = $transport === 'air' ? self::AIR_VOLUME_DIVISOR : self::VOLUME_DIVISOR;
        $volumeWeight = round($l * $w * $h / $divisor, 2);
        $chargeable = max($weight, $volumeWeight);

        $base = [
            'weight' => round($weight, 2),
            'volume_weight' => $volumeWeight,
            'chargeable_weight' => round($chargeable, 2),
        ];

        $model = match ($transport) {
            'air' => Avia::class,
            'railway' => Zh::class,
            default => Avto::class,
        };
        $route = $model::where('city_from', $fromId)->where('city_to', $toId)->first();
        if (! $route || (float) $route->price <= 0) {
            return ['found' => false] + $base;
        }

        $rate = (float) $route->price;
        $notes = [];

        if ($transport === 'air') {
            $price = $rate + max(0, $chargeable - self::AIR_BASE_KG) * self::AIR_EXTRA_PER_KG;
            $coef = 1.0;
        } else {
            $price = self::MIN_CHARGE + max(0, $chargeable - self::MIN_CHARGE_KG) * $rate;
            if ($chargeable <= self::MIN_CHARGE_KG) {
                $notes[] = 'Минимальный сбор за отправление до 20 кг';
            }

            $coef = 1.0;
            $limits = self::OVERSIZE_LIMITS;
            if ($l > $limits['length'] || $w > $limits['width'] || $h > $limits['height'] || $weight > $limits['weight']) {
                $coef = self::OVERSIZE_COEF;
                $notes[] = 'Негабаритный груз — коэффициент 1,3';
            }
            if (! empty($cargo['non_stackable'])) {
                $coef = max($coef, self::NON_STACKABLE_COEF);
                $notes[] = 'Нештабелируемый груз — коэффициент 2';
            }
            $price *= $coef;
        }

        return $base + [
            'found' => true,
            'price' => (int) round($price),
            'rate' => $rate,
            'time' => (string) $route->time,
            'coefficient' => $coef,
            'notes' => $notes,
        ];
    }
}
