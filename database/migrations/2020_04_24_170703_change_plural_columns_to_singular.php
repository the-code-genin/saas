<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangePluralColumnsToSingular extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->text('requirement');
        });

        Schema::table('students', function (Blueprint $table) {
            $table->enum('available_for_job', ['true', 'false'])->default('true');
        });

        DB::update('UPDATE jobs SET requirement = requirements', []);
        DB::update('UPDATE students SET available_for_job = available_for_jobs', []);

        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn('available_for_jobs');
        });

        Schema::table('jobs', function (Blueprint $table) {
            $table->dropColumn('requirements');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->text('requirements');
        });

        Schema::table('students', function (Blueprint $table) {
            $table->enum('available_for_jobs', ['true', 'false'])->default('true');
        });

        DB::update('UPDATE jobs SET requirements = requirement', []);
        DB::update('UPDATE students SET available_for_jobs = available_for_job', []);

        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn('available_for_job');
        });

        Schema::table('jobs', function (Blueprint $table) {
            $table->dropColumn('requirement');
        });
    }
}
