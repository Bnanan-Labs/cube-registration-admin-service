<?php

use App\Services\Wca\Enums\Event;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('wca_event_id')->nullable();
            $table->string('title');
            $table->integer('qualification_limit')->nullable();
            $table->integer('cutoff_limit')->nullable();
            $table->integer('competitor_limit')->nullable();
            $table->integer('time_limit')->nullable();
            $table->integer('fee')->nullable();
            $table->foreignUuid('competition_id');

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
        Schema::dropIfExists('events');
    }
}
