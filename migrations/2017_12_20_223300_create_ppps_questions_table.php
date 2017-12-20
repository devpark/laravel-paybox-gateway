<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePppsQuestionsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ppps_questions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('numquestion', 32)->nullable()->index();
            $table->string('version', 16);
            $table->string('hash', 16);
            $table->string('type', 16);
            $table->string('site', 32);
            $table->string('rang', 16);
            $table->string('dateq', 32);
            $table->string('activite', 32)->nullable();
            $table->string('reference', 64)->nullable();
            $table->string('refabonne', 64)->nullable();
            $table->string('montant', 32)->nullable();
            $table->string('devise', 12)->nullable();
            $table->string('porteur', 64)->nullable();
            $table->string('dateval', 4)->nullable();
            $table->string('cvv', 16)->nullable();
            $table->string('numappel', 32)->nullable();
            $table->string('numtrans', 32)->nullable();
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
        Schema::dropIfExists('ppps_questions');
    }
}
