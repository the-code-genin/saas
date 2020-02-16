<?php

use Cradle\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOrganizationsTable extends Migration
{
    public function up()
    {
        $this->schema->create('organizations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 300)->unique();
            $table->string('address', 300);
            $table->text('description');
            $table->integer('category_id')->unsigned();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('category_id')
                ->references('id')
                ->on('organization_categories');
        });
    }

    public function down()
    {
        $this->schema->drop('organizations');
    }
}
