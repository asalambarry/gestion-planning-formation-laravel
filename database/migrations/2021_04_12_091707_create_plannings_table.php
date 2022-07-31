<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlanningsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plannings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cours_id');
            $table->dateTime('date_debut')->nullable();
            $table->dateTime('date_fin')->nullable();

            $table->foreign('cours_id')
                ->references('id')->on('cours');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('plannings', function (Blueprint $table) {
           $table->dropForeign(['cours_id']);
        });

        Schema::dropIfExists('plannings');
    }
}
