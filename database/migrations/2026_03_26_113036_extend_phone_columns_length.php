<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('sender_phone', 100)->change();
            $table->string('recipient_phone', 100)->change();
        });

        Schema::table('recipient_templates', function (Blueprint $table) {
            $table->string('recipient_phone', 100)->change();
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('sender_phone', 20)->change();
            $table->string('recipient_phone', 20)->change();
        });

        Schema::table('recipient_templates', function (Blueprint $table) {
            $table->string('recipient_phone', 20)->change();
        });
    }
};
