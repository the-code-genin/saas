<?php

use Cradle\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateStudentsTable extends Migration
{
    public function up()
    {
        $this->schema->create('students', function (Blueprint $table) {
            $table->increments('id');
            $table->string('full_name', 300);
            $table->enum('availability', ['freelance', 'part_time']);
            $table->decimal('hourly_rate', 10, 2);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        $this->schema->drop('students');
    }
}
