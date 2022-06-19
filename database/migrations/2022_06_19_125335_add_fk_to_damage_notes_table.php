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
        Schema::table('damage_notes', function (Blueprint $table) {
            $table->foreign('object_type_id')
                ->references('id')
                ->on('object_types')
                ->nullOnDelete()
                ->cascadeOnUpdate();
            $table->foreign('city_id')
                ->references('id')
                ->on('cities')
                ->nullOnDelete()
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
        Schema::table('damage_notes', function (Blueprint $table) {
            $table->dropForeign(['object_type_id']);
            $table->dropForeign(['city_id']);
        });
    }
};
