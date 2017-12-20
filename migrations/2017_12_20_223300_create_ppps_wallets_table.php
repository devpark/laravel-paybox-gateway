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
            $table->string('paybox_id', 64)->index();
            $table->string('customer_id', 64)->index();
            $table->timestamp('card_expiration_date');
            $table->string('card_number', 32);
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
