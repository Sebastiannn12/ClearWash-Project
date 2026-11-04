<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('membership_transactions', function (Blueprint $table) {
            $table->id();

            $table->string('reference_number')->unique()->nullable();

            $table->foreignId('member_id')->constrained('members');

            $table->foreignId('membership_level_id')->constrained('membership_levels');

            $table->string('type');
            $table->string('status')->default('Pending');

            $table->decimal('amount_due', 10, 2);
            $table->unsignedInteger('validity_days');


            $table->timestamp('approved_at')->nullable();
            $table->timestamp('expires_at')->nullable();

            $table->text('notes')->nullable();

            $table->timestamps();


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('membership_transactions');
    }
};
