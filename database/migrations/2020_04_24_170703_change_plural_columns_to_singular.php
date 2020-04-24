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
        Schema::table('students', function (Blueprint $table) {
            $table->enum('available_for_job', ['true', 'false'])->default('true');
        });

        DB::update('UPDATE students SET available_for_job = available_for_jobs', []);

        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn('available_for_jobs');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('students', function (Blueprint $table) {
            $table->enum('available_for_jobs', ['true', 'false'])->default('true');
        });

        DB::update('UPDATE students SET available_for_jobs = available_for_job', []);

        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn('available_for_job');
        });
    }
}
