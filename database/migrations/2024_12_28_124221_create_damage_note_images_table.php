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
        Schema::create('damage_note_images', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('damage_note_id')->unsigned();
            $table->string('file_name');
            $table->string('hash_file_name');
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
        Schema::dropIfExists('damage_note_images');
    }
};
