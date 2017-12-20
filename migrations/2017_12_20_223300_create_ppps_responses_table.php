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
            $table->string('numquestion', 32)->index();
            $table->string('site', 32);
            $table->string('rang', 16);
            $table->string('codereponse', 16);
            $table->string('numappel', 32);
            $table->string('numtrans', 32)->index();
            $table->string('autorisation', 32);
            $table->string('remise', 32)->nullable();
            $table->string('typecarte', 32)->nullable();
            $table->string('pays', 16)->nullable();
            $table->string('porteur', 64)->nullable();
            $table->string('refabonne', 64)->index()->nullable();
            $table->string('commentaire', 255);
            $table->string('status', 16)->nullable();
            $table->string('sha', 64)->nullable();
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
