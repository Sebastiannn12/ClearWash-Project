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
        Schema::create('paymets', function (Blueprint $table) {
            $table->id();

            $table->foreignId('membership_transaction_id')
                ->constrained('membership_transactions');

            $table->string('payment_reference')->nullable();

            $table->decimal('amount', 10, 2);
            $table->string('method');

            $table->timestamp('paid_at');

            $table->foreignId('received_by')
                ->nullable()
                ->constrained('users');

            $table->text('notes')->nullable();

            $table->timestamps();s
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paymets');
    }
};
