<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->foreignId('membership_category_id')
                ->nullable()
                ->after('category')
                ->constrained('membership_categories')
                ->restrictOnDelete();
            $table->foreignId('membership_level_id')
                ->nullable()
                ->after('membership_level')
                ->constrained('membership_levels')
                ->restrictOnDelete();
        });

        $categories = DB::table('membership_categories')
            ->pluck('id', 'name')
            ->mapWithKeys(fn ($id, $name) => [Str::lower(trim($name)) => $id]);
        $levels = DB::table('membership_levels')
            ->pluck('id', 'name')
            ->mapWithKeys(fn ($id, $name) => [Str::lower(trim($name)) => $id]);

        DB::table('members')
            ->select(['id', 'category', 'membership_level'])
            ->orderBy('id')
            ->chunkById(200, function ($members) use ($categories, $levels): void {
                foreach ($members as $member) {
                    DB::table('members')
                        ->where('id', $member->id)
                        ->update([
                            'membership_category_id' => $categories->get(Str::lower(trim($member->category))),
                            'membership_level_id' => $levels->get(Str::lower(trim($member->membership_level))),
                        ]);
                }
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropConstrainedForeignId('membership_category_id');
            $table->dropConstrainedForeignId('membership_level_id');
        });
    }
};
