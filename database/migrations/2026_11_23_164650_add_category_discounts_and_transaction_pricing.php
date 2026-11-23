<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('membership_categories', function (Blueprint $table): void {
            $table->decimal('discount_percentage', 5, 2)->default(0);
        });

        Schema::table('membership_transactions', function (Blueprint $table): void {
            $table->decimal('original_fee', 10, 2)->nullable();
            $table->decimal('discount_percentage', 5, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->string('category_name')->nullable();
            $table->string('level_name')->nullable();
        });

        DB::table('membership_transactions')->whereNull('original_fee')->update(['original_fee' => DB::raw('amount_due')]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('membership_transactions', function (Blueprint $table): void {
            $table->dropColumn(['original_fee', 'discount_percentage', 'discount_amount', 'category_name', 'level_name']);
        });

        Schema::table('membership_categories', function (Blueprint $table): void {
            $table->dropColumn('discount_percentage');
        });
    }
};
