<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Новые тарифы из КП LogExim Express (2026-10): авто и ж/д из Алматы,
 * цена за 1 кг и срок доставки. Остальные направления (обратно в Алматы,
 * из Астаны) и авиа — без изменений. Дальше правятся в /admin/tariffs.
 */
return new class extends Migration
{
    private const AVTO = [
        'Караганда' => [120, '2 дня'],
        'Астана' => [120, '2 дня'],
        'Кокшетау' => [160, '3-5 дней'],
        'Петропавловск' => [160, '3-5 дней'],
        'Костанай' => [160, '3-5 дней'],
        'Усть-Каменогорск' => [160, '3-4 дня'],
        'Семей' => [160, '3-4 дня'],
        'Павлодар' => [160, '3-5 дней'],
        'Уральск' => [210, '5-7 дней'],
        'Актау' => [220, '6-8 дней'],
        'Актобе' => [200, '5-6 дней'],
        'Атырау' => [210, '5-6 дней'],
        'Кызылорда' => [160, '2-3 дня'],
        'Шымкент' => [110, '2 дня'],
        'Тараз' => [110, '2 дня'],
        'Жезказган' => [190, '3-5 дней'],
        'Талдыкорган' => [120, '2 дня'],
    ];

    private const ZH = [
        'Актау' => [120, '7-15 дней'],
        'Актобе' => [120, '7-12 дней'],
        'Атырау' => [120, '7-14 дней'],
    ];

    public function up(): void
    {
        $cities = [];
        foreach (DB::table((new \App\Models\CityDelivery)->getTable())->get(['id', 'title']) as $c) {
            $cities[mb_strtolower(trim($c->title))] = (int) $c->id;
        }
        $id = fn (string $title) => $cities[mb_strtolower($title)]
            ?? ($title === 'Жезказган' ? ($cities['джезказган'] ?? null) : null);

        $from = $id('Алматы');
        if ($from === null) {
            return; // пустой справочник (тестовая база)
        }

        foreach (['avto' => self::AVTO, 'zh' => self::ZH] as $table => $rows) {
            foreach ($rows as $title => [$price, $time]) {
                $to = $id($title);
                if ($to === null) {
                    continue;
                }
                $match = ['city_from' => $from, 'city_to' => $to];
                if (DB::table($table)->where($match)->exists()) {
                    DB::table($table)->where($match)->update(['price' => $price, 'time' => $time]);
                } else {
                    DB::table($table)->insert($match + ['price' => $price, 'time' => $time]);
                }
            }
        }
    }

    public function down(): void
    {
    }
};
