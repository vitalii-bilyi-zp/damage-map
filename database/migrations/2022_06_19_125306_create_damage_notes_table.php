<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\DamageNote;

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
            $table->date('date');
            $table->bigInteger('object_type_id')->unsigned()->index()->nullable();
            $table->bigInteger('community_id')->unsigned()->index()->nullable();
            $table->string('city')->nullable();
            $table->string('street')->nullable();
            $table->string('building_number')->nullable();
            $table->unsignedInteger('floors')->nullable();
            $table->decimal('area', 12, 2)->nullable();
            $table->enum('damage_type', array_keys(DamageNote::DAMAGE_TYPES_MAPPING))->nullable();
            $table->bigInteger('repair_type_id')->unsigned()->index()->nullable();
            $table->decimal('restoration_cost', 15, 2)->nullable();
            $table->decimal('predicted_restoration_cost', 15, 2)->nullable();
            $table->text('comment')->nullable();
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
