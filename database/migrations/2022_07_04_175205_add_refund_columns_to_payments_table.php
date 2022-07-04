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
        Schema::table('payments', function (Blueprint $table) {
            $table->timestamp('refunded_at')->nullable()->index()->after('captured_at');
            $table->integer('refunded_amount')->default(0)->after('total');
            $table->string('refund_id')->nullable()->after('intent_secret');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['refunded_at', 'refunded_amount', 'refund_id']);
        });
    }
};
