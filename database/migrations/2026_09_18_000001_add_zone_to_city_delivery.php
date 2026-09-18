<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Зона доставки у города. Города с одинаковой непустой зоной считаются одной
 * областью — доставка между ними идёт без склада (напр. «Алматы» для Алматы
 * и городов области).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('city_delivery', 'zone')) {
            Schema::table('city_delivery', function (Blueprint $table) {
                $table->string('zone')->nullable()->after('title');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('city_delivery', 'zone')) {
            Schema::table('city_delivery', function (Blueprint $table) {
                $table->dropColumn('zone');
            });
        }
    }
};
