<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            // paid_by nullable: diisi kalau yang nalangin user terdaftar
            $table->foreignId('paid_by')->nullable()->change();
            // Nama payer guest: diisi kalau yang nalangin non-user
            $table->string('guest_payer_name')->nullable()->after('paid_by');
        });
    }

    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->foreignId('paid_by')->nullable(false)->change();
            $table->dropColumn('guest_payer_name');
        });
    }
};
