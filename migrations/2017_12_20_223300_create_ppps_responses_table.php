<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePppsResponsesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ppps_responses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('numquestion', 10)->index();
            $table->string('site', 7);
            $table->string('rang', 3);
            $table->string('codereponse', 5);
            $table->string('numappel', 10);
            $table->string('numtrans', 10)->index();
            $table->string('autorisation', 10);
            $table->string('remise', 9)->nullable();
            $table->string('typecarte', 10)->nullable();
            $table->string('pays', 3)->nullable();
            $table->string('porteur', 19)->nullable();
            $table->string('refabonne', 250)->nullable();
            $table->string('commentaire', 100);
            $table->string('status', 32)->nullable();
            $table->string('sha', 40)->nullable();
            $table->unsignedInteger('wallet_id')->index()->nullable();
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
        Schema::dropIfExists('ppps_responses');
    }
}
