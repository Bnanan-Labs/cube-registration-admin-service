<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompetitorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('competitors', function (Blueprint $table) {
            $table->id();
            $table->string('first_name')->index();
            $table->string('last_name')->index();
            $table->string('wca_id')->index();
            $table->string('email');
            $table->string('registration_status')->default('PENDING')->index();
            $table->string('payment_status')->default('MISSING_PAYMENT')->index();
            $table->string('nationality')->index();
            $table->boolean('has_podium_potential')->default(false)->index();
            $table->boolean('is_eligible_for_prizes')->default(false);
            $table->foreignId('financial_book_id');
            $table->foreignId('competition_id');

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
        Schema::dropIfExists('competitors');
    }
}
