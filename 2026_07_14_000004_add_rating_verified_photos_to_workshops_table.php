<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('workshops', function (Blueprint $table) {
            $table->decimal('rating_avg', 3, 2)->default(0)->after('description');
            $table->unsignedInteger('total_reviews')->default(0)->after('rating_avg');
            $table->boolean('is_verified')->default(false)->after('total_reviews');
            $table->json('photos')->nullable()->after('is_verified');
        });
    }

    public function down(): void
    {
        Schema::table('workshops', function (Blueprint $table) {
            $table->dropColumn(['rating_avg', 'total_reviews', 'is_verified', 'photos']);
        });
    }
};
