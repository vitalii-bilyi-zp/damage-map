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
        Schema::table('users', function (Blueprint $table) {
            $table->bigInteger('region_id')->unsigned()->index()->nullable()->after('api_token');
            $table->bigInteger('district_id')->unsigned()->index()->nullable()->after('region_id');
            $table->bigInteger('community_id')->unsigned()->index()->nullable()->after('district_id');

            $table->foreign('region_id')
                ->references('id')
                ->on('regions')
                ->nullOnDelete()
                ->cascadeOnUpdate();

            $table->foreign('district_id')
                ->references('id')
                ->on('districts')
                ->nullOnDelete()
                ->cascadeOnUpdate();

            $table->foreign('community_id')
                ->references('id')
                ->on('communities')
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
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['region_id']);
            $table->dropForeign(['district_id']);
            $table->dropForeign(['community_id']);

            $table->dropColumn('region_id');
            $table->dropColumn('district_id');
            $table->dropColumn('community_id');
        });
    }
};
