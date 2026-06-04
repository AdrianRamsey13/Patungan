<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('event_members', function (Blueprint $table) {
            // user_id jadi nullable supaya bisa guest (tidak punya akun)
            $table->foreignId('user_id')->nullable()->change();
            // Nama tamu (diisi kalau bukan user terdaftar)
            $table->string('guest_name')->nullable()->after('user_id');
        });
    }

    public function down(): void
    {
        Schema::table('event_members', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable(false)->change();
            $table->dropColumn('guest_name');
        });
    }
};
