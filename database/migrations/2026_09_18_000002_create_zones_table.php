<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Справочник зон доставки. Название зоны проставляется городам (city_delivery.zone),
 * города с одной зоной — одна область (доставка без склада). Отдельный список нужен,
 * чтобы у города выбирать зону из выпадающего списка, а не вписывать руками.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('zones')) {
            Schema::create('zones', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->timestamps();
            });
        }

        // Переносим уже проставленные у городов зоны в справочник.
        $existing = DB::table('city_delivery')
            ->whereNotNull('zone')->where('zone', '!=', '')
            ->distinct()->pluck('zone');
        foreach ($existing as $name) {
            DB::table('zones')->insertOrIgnore(['name' => $name, 'created_at' => now(), 'updated_at' => now()]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('zones');
    }
};
