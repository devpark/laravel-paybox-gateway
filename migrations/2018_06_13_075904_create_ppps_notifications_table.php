<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePppsNotificationsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ppps_notifications', function (Blueprint $table) {
            $table->increments('id');
            $table->string('numquestion', 10)->nullable()->index();
            $table->string('reference', 250)->nullable();
            $table->longText('data');
            $table->string('status')->default('pending')->index();
            $table->unsignedInteger('tries')->default(0);
            $table->string('return_code', 3)->nullable();
            $table->longText('return_content')->nullable();
            $table->timestamp('notified_at')->nullable();
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
        Schema::drop('ppps_notifications');
    }
}
