<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('expense_splits', function (Blueprint $table) {
            $table->unsignedBigInteger('amount_paid')->default(0)->after('amount_owed');
        });

        // Backfill: splits yang is_paid=true → amount_paid = amount_owed
        DB::statement('UPDATE expense_splits SET amount_paid = amount_owed WHERE is_paid = 1');
    }

    public function down(): void
    {
        Schema::table('expense_splits', function (Blueprint $table) {
            $table->dropColumn('amount_paid');
        });
    }
};
