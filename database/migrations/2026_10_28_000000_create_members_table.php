<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->string('membership_number')->nullable()->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->nullable();
            $table->string('phone', 30);
            $table->string('category', 30);
            $table->string('membership_level', 30);
            $table->text('address')->nullable();
            $table->date('joined_on');
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['last_name', 'first_name']);
            $table->index(['category', 'membership_level']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
