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
        Schema::table('competitors', function (Blueprint $table) {
            $table->integer('number_of_competitions')->after('guests')->index()->default(0);
            $table->string('medals')->after('number_of_competitions')->nullable();
            $table->string('records')->after('medals')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('competitors', function (Blueprint $table) {
            $table->dropColumn(['number_of_competitions', 'medals', 'records']);
        });
    }
};
