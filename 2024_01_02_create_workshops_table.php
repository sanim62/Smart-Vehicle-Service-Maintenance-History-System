<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workshops', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('owner_name');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->text('address');
            $table->string('city');
            $table->string('license_number')->unique()->nullable();
            $table->text('service_categories'); // JSON or comma-separated
            $table->string('status')->default('active'); // active, inactive, suspended
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workshops');
    }
};
