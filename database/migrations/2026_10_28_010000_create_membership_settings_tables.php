<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('membership_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('membership_levels', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->decimal('fee', 10, 2)->nullable();
            $table->unsignedInteger('validity_days')->nullable();
            $table->text('benefits')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        $now = now();

        DB::table('membership_categories')->insert(
            collect(['Student', 'Regular', 'Senior Citizen', 'Corporate'])
                ->map(fn ($name) => ['name' => $name, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now])
                ->all()
        );

        DB::table('membership_levels')->insert(
            collect(['Basic', 'Silver', 'Gold', 'Platinum'])
                ->map(fn ($name) => ['name' => $name, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now])
                ->all()
        );

        if (Schema::hasTable('members')) {
            DB::table('members')->where('category', 'Individual')->update(['category' => 'Regular']);
            DB::table('members')->where('membership_level', 'Plus')->update(['membership_level' => 'Silver']);
            DB::table('members')->where('membership_level', 'Premium')->update(['membership_level' => 'Platinum']);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('membership_levels');
        Schema::dropIfExists('membership_categories');
    }
};
