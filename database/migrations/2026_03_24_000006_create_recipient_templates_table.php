<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recipient_templates', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('recipient_name');
            $table->string('recipient_phone', 20);
            $table->string('company')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('country', 100)->nullable();
            $table->string('region', 100)->nullable();
            $table->string('district', 100)->nullable();
            $table->text('address')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recipient_templates');
    }
};
