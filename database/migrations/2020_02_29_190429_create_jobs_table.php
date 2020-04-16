<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobsTable extends Migration
{
    public function up()
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->string('title', '300');
            $table->text('description');
            $table->text('requirements');
            $table->text('location');
            $table->text('about_position');
            $table->text('duties');
            $table->enum('category', ['remote', 'weekend', 'weekday']);
            $table->text('about_organization');
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::drop('jobs');
    }
}
