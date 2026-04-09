<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inflation_indices', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('year');
            $table->unsignedTinyInteger('month')->comment('Місяць: 1–12');
            $table->decimal('index_value', 6, 4);
            $table->enum('source_type', ['official', 'forecast', 'extrapolated'])->default('forecast');
            $table->string('source_description', 500)->nullable();
            $table->timestamps();

            $table->unique(['year', 'month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inflation_indices');
    }
};
