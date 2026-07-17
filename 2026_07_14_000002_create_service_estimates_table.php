<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_estimates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workshop_id')->constrained()->onDelete('cascade');
            $table->string('service_type');
            $table->decimal('min_price', 10, 2)->default(0);
            $table->decimal('max_price', 10, 2)->default(0);
            $table->decimal('duration_hours', 4, 1)->default(1); // estimated hours
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['workshop_id', 'service_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_estimates');
    }
};
