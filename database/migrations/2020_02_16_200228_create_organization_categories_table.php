<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrganizationCategoriesTable extends Migration
{
    public function up()
    {
        Schema::create('organization_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 300);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::drop('organization_categories');
    }
}
