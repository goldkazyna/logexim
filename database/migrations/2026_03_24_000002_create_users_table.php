<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('bin');
            $table->string('password');
            $table->string('ip');
            $table->dateTime('date');
            $table->integer('activate');
            $table->string('activate_code');
            $table->string('company_name');
            $table->string('phone');
            $table->string('email');
            $table->string('director_name');
            $table->string('address');
            $table->string('city');
            $table->string('region');
            $table->string('country');
            $table->string('district');
            $table->string('restore_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
