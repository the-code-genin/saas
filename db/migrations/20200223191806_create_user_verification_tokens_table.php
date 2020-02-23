<?php

use Cradle\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserVerificationTokensTable extends Migration
{
    public function up()
    {
        $this->schema->create('user_verification_tokens', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->string('token', 255);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        $this->schema->drop('user_verification_tokens');
    }
}
