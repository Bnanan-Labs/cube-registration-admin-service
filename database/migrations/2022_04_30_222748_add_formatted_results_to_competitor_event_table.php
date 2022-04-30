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
        Schema::table('competitor_event', function (Blueprint $table) {
            $table->string('best_single_formatted')->nullable()->after('best_single');
            $table->string('best_average_formatted')->nullable()->after('best_average');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('competitor_event', function (Blueprint $table) {
            $table->dropColumn(['best_single_formatted', 'best_average_formatted']);
        });
    }
};
