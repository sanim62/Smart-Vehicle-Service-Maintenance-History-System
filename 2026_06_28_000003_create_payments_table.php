<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('service_id')->constrained()->onDelete('cascade');
            $table->foreignId('workshop_id')->constrained()->onDelete('cascade');
            $table->decimal('total_amount', 10, 2);
            $table->decimal('commission_rate', 5, 2)->default(2.50);
            $table->decimal('commission_amount', 10, 2);
            $table->decimal('workshop_amount', 10, 2);
            $table->string('payment_method')->default('card');
            $table->string('transaction_id')->unique();
            $table->enum('status', ['completed', 'pending', 'failed', 'refunded'])->default('completed');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
