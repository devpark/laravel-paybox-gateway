<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterPppsQuestionsTableAdd3dsecure extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ppps_questions', function (Blueprint $table) {
            $table->string('id3d', 20)->nullable();
            $table->string('3dcavv', 28)->nullable();
            $table->string('3dcavvalgo', 64)->nullable();
            $table->string('3deci', 2)->nullable();
            $table->string('3denrolled', 1)->nullable();
            $table->string('3derror', 6)->nullable();
            $table->string('3dsignval', 1)->nullable();
            $table->string('3dstatus', 1)->nullable();
            $table->string('3dxid', 28)->nullable();
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ppps_questions', function (Blueprint $table) {
            $table->dropColumn('id3d');
            $table->dropColumn('3dcavv');
            $table->dropColumn('3dcavvalgo');
            $table->dropColumn('3deci');
            $table->dropColumn('3denrolled');
            $table->dropColumn('3derror');
            $table->dropColumn('3dsignval');
            $table->dropColumn('3dstatus');
            $table->dropColumn('3dxid');
        });
    }
}
