<?php

use Cradle\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUsersTable extends Migration
{
    public function up()
    {
        $this->schema->create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('email', 255)->unique();
            $table->string('password', 255);
            $table->text('address');
            $table->string('phone_number', 30);
            $table->integer('userable_id')->unsigned();
            $table->string('userable_type', 300);
            $table->enum('status', ['active', 'banned'])->default('active');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down()
    {
        $this->schema->drop('users');
    }
}
