<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompetitorEventTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('competitor_event', function (Blueprint $table) {
            $table->id();
            $table->integer('best_single')->nullable();
            $table->integer('best_average')->nullable();
            $table->integer('ranking')->nullable();
            $table->foreignId('competitor_id');
            $table->foreignId('event_id');

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
        Schema::dropIfExists('competitor_event');
    }
}
