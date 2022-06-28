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
        Schema::create('damage_notes', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('object_type_id')->unsigned()->index()->nullable();
            $table->bigInteger('community_id')->unsigned()->index()->nullable();
            $table->string('city');
            $table->string('street');
            $table->string('building_number');
            $table->enum('damage_type', ['low', 'medium', 'high']);
            $table->decimal('restoration_cost', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('damage_notes');
    }
};
