<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tm_order', function (Blueprint $table) {
            $table->id();
            $table->string('track_number');
            $table->string('name_from');
            $table->string('name_to');
            $table->date('date_from');
            $table->date('date_to');
            $table->integer('status')->default(0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tm_order');
    }
};
