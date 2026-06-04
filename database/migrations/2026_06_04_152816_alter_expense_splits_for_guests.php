<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('expense_splits', function (Blueprint $table) {
            // user_id nullable untuk guest splits
            $table->foreignId('user_id')->nullable()->change();
            // Nama tamu (diisi kalau split ini milik non-user)
            $table->string('guest_name')->nullable()->after('user_id');
        });
    }

    public function down(): void
    {
        Schema::table('expense_splits', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable(false)->change();
            $table->dropColumn('guest_name');
        });
    }
};
