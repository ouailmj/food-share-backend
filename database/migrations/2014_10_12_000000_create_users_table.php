<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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
            $table->bigIncrements('id');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('passwordtoken')->nullable();
            $table->boolean('verified')->default(false);
            $table->string('phone');
            $table->date('date_naissance');
            $table->boolean('sexe');
            $table->string('address');
            $table->string('ville');
            $table->string('code_postal');
            $table->string('localization');
            $table->integer('nombre signalisation');
            $table->timestamp('email_verified_at')->nullable();
            $table->bigInteger('image_id')->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
