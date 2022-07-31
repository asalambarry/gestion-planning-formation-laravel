<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nom', 40)->nullable();
            $table->string('prenom', 40)->nullable();
            $table->string('login', 30)->unique();
            $table->string('mdp', 60);
            $table->unsignedBigInteger('formation_id')->nullable();

            $table->foreign('formation_id')
                ->references('id')->on('formations');
        });

        if(env('DB_CONNECTION') === 'sqlite') {
            DB::statement('ALTER TABLE users ADD COLUMN type varchar(10) CHECK (`type` in (NULL, \'etudiant\',\'enseignant\',\'admin\'));');
        }
        else {
            DB::statement('ALTER TABLE users ADD COLUMN type varchar(10) CHECK (type in (NULL, \'etudiant\',\'enseignant\',\'admin\'));');
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['formation_id']);
        });

        Schema::dropIfExists('users');
    }
}
