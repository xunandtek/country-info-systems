<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->id();

            $table->string('cca2', 2)->unique();
            $table->string('cca3', 3)->unique();

            $table->string('name_common')->index();
            $table->string('name_official')->nullable();

            $table->string('capital')->nullable()->index();
            $table->string('region')->nullable()->index();
            $table->string('subregion')->nullable();

            $table->unsignedBigInteger('population')->nullable()->index();

            $table->string('flag_png')->nullable();
            $table->string('flag_svg')->nullable();

            $table->timestamps();

            // MySQL FULLTEXT for search (name_common + capital)
            $table->fullText(['name_common', 'capital']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};
