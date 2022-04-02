<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventStaffTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_staff', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('event_id');
            $table->foreignUuid('staff_id');
            $table->boolean('can_scramble')->default(false);
            $table->boolean('is_priority')->default(false);
            $table->integer('duties')->default(0);
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
        Schema::dropIfExists('event_staff');
    }
}
