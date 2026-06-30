<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('make');               // Toyota, Honda, etc.
            $table->string('model');              // Corolla, Civic, etc.
            $table->year('year');
            $table->string('registration_number')->unique();
            $table->string('chassis_number')->unique();
            $table->string('color')->nullable();
            $table->string('fuel_type')->default('petrol'); // petrol, diesel, electric, hybrid
            $table->integer('mileage')->default(0);
            $table->string('status')->default('active'); // active, inactive
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
