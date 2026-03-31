<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('description_templates', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('template_name');
            $table->string('description')->nullable();
            $table->integer('quantity')->nullable();
            $table->decimal('weight', 10, 2)->nullable();
            $table->decimal('volume_weight', 10, 2)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('description_templates');
    }
};
