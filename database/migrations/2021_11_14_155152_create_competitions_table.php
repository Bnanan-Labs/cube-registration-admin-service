<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompetitionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('competitions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_active')->default(false)->index();
            $table->timestamp('registration_starts')->nullable();
            $table->timestamp('registration_ends')->nullable();
            $table->timestamp('volunteer_registration_starts')->nullable();
            $table->timestamp('volunteer_registration_ends')->nullable();
            $table->integer('base_fee')->nullable();
            $table->integer('guest_fee')->nullable();
            $table->string('currency')->nullable();
            $table->integer('competitor_limit')->nullable();
            $table->integer('spectator_limit')->nullable();
            $table->foreignUuid('financial_book_id');

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
        Schema::dropIfExists('competitions');
    }
}
