<?php

use Cradle\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateJobSkillsTable extends Migration
{
    public function up()
    {
        $this->schema->create('job_skills', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('job_id')->unsigned();
            $table->string('name', '300');
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('job_id')
                ->references('id')
                ->on('jobs')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        $this->schema->drop('job_skills');
    }
}
