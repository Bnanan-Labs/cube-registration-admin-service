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
            $table->dropColumn('ranking');
            $table->integer('world_rank_single')->after('best_average')->nullable();
            $table->integer('continental_rank_single')->after('best_average')->nullable();
            $table->integer('national_rank_single')->after('best_average')->nullable();
            $table->integer('competition_rank_single')->after('best_average')->nullable()->index();
            $table->integer('world_rank_average')->after('best_average')->nullable();
            $table->integer('continental_rank_average')->after('best_average')->nullable();
            $table->integer('national_rank_average')->after('best_average')->nullable();
            $table->integer('competition_rank_average')->after('best_average')->nullable()->index();
            $table->timestamp('synced_at')->after('event_id')->nullable()->index();
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
            $table->integer('ranking')->after('best_average')->nullable();
            $table->dropColumn(['world_rank_single','continental_rank_single','national_rank_single','competition_rank_single','world_rank_average','continental_rank_average','national_rank_average','competition_rank_average','synced_at']);
        });
    }
};
