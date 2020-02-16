<?php

use Cradle\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserApiTokensTable extends Migration
{
    public function up()
    {
        $this->schema->create('user_api_tokens', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->string('token', 255);
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        $this->schema->drop('user_api_tokens');
    }
}
