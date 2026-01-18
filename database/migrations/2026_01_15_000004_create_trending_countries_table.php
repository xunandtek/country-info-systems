<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('trending_countries', function (Blueprint $table) {
            $table->foreignId('country_id')->primary()->constrained('countries')->cascadeOnDelete();
            $table->unsignedInteger('views_24h')->default(0);
            $table->dateTime('calculated_at')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trending_countries');
    }
};
