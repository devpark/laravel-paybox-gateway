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
            $table->string('numquestion', 10)->nullable()->index();
            $table->string('version', 5);
            $table->string('hash', 40);
            $table->string('type', 5);
            $table->string('site', 7);
            $table->string('rang', 3);
            $table->string('dateq', 14);
            $table->string('activite', 3)->nullable();
            $table->string('reference', 250)->nullable();
            $table->string('refabonne', 250)->nullable();
            $table->string('montant', 10)->nullable();
            $table->string('devise', 3)->nullable();
            $table->string('porteur', 19)->nullable();
            $table->string('dateval', 4)->nullable();
            $table->string('cvv', 4)->nullable();
            $table->string('numappel', 10)->nullable();
            $table->string('numtrans', 10)->nullable();
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
        Schema::dropIfExists('ppps_questions');
    }
}
