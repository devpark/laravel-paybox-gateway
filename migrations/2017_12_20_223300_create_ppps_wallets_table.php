<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePppsWalletsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ppps_wallets', function (Blueprint $table) {
            $table->increments('id');
            $table->string('subscriber_id', 191)->index();
            $table->string('card_number', 19);
            $table->timestamp('card_expiration_date')->nullable();
            $table->string('paybox_id', 19)->nullable();
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
        Schema::dropIfExists('ppps_wallets');
    }
}
