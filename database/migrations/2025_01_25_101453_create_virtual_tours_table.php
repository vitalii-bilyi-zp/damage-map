<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('virtual_tours', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('description')->nullable();
            $table->bigInteger('damage_note_id')->unsigned()->nullable();
            $table->string('image_file_name')->nullable();
            $table->string('image_hash_file_name')->nullable();
            $table->string('audio_file_name')->nullable();
            $table->string('audio_hash_file_name')->nullable();
            $table->timestamps();

            $table->foreign('damage_note_id')
                ->references('id')
                ->on('damage_notes')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('virtual_tours');
    }
};
