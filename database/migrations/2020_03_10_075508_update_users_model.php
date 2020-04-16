<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateUsersModel extends Migration
{
    public function up()
    {
        Schema::table('students', function (Blueprint $table) {
            $table->enum('available_for_jobs', ['true', 'false'])->default('true');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->text('bio')->nullable();
        });

        Schema::create('student_skills', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('student_id')->unsigned();
            $table->string('name', '300');
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('student_id')
                ->references('id')
                ->on('students')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn('available_for_jobs');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('bio');
        });

        Schema::drop('student_skills');
    }
}
