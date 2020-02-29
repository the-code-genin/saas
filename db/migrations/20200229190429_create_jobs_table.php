<?php

use Cradle\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateJobsTable extends Migration
{
    public function up()
    {
        $this->schema->create('jobs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('organization_id')->unsigned();
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

            $table->foreign('organization_id')
                ->references('id')
                ->on('organizations')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        $this->schema->drop('jobs');
    }
}
