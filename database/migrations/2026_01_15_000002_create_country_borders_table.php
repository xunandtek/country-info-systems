<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('country_borders', function (Blueprint $table) {
            $table->unsignedBigInteger('country_id');
            $table->unsignedBigInteger('border_country_id');

            $table->foreign('country_id')->references('id')->on('countries')->cascadeOnDelete();
            $table->foreign('border_country_id')->references('id')->on('countries')->cascadeOnDelete();

            $table->unique(['country_id', 'border_country_id']);
            $table->index(['country_id']);
            $table->index(['border_country_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('country_borders');
    }
};
