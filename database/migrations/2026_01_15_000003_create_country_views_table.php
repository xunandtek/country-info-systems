<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('country_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained('countries')->cascadeOnDelete();
            $table->dateTime('viewed_at')->index();
            $table->timestamps();

            $table->index(['country_id', 'viewed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('country_views');
    }
};
