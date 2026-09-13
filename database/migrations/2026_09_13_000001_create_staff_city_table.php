<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Города обслуживания сотрудника (курьер/агент) — связь многие-ко-многим
 * со справочником city_delivery. Один сотрудник может работать по нескольким
 * городам; по городу можно найти всех, кто его обслуживает.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('staff_city')) {
            return;
        }

        Schema::create('staff_city', function (Blueprint $table) {
            $table->unsignedBigInteger('staff_id');
            $table->unsignedBigInteger('city_id');
            $table->primary(['staff_id', 'city_id']);
            $table->index('city_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_city');
    }
};
