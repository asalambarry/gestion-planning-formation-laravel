<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoursTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cours', function (Blueprint $table) {
            $table->id();
            $table->string('intitule', 50);
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('formation_id')->nullable();
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')->on('users');
            $table->foreign('formation_id')
                ->references('id')->on('formations');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cours', function (Blueprint $table) {
           $table->dropForeign(['user_id', 'formation_id']);
        });

        Schema::dropIfExists('cours');
    }
}
