<?php

use Cradle\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOrganizationCategoriesTable extends Migration
{
    public function up()
    {
        $this->schema->create('organization_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 300);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        $this->schema->drop('organization_categories');
    }
}
