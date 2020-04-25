<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewUserAvailability extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('students', function(Blueprint $table) {
            $table->text('_temp_column')->nullable();
        });

        DB::update('UPDATE students SET _temp_column = availability', []);

        Schema::table('students', function(Blueprint $table) {
            $table->dropColumn('availability');
        });

        Schema::table('students', function(Blueprint $table) {
            $table->enum('availability', ['freelance', 'full_time', 'part_time', 'week_days', 'week_ends'])->nullable();
        });

        DB::update('UPDATE students SET availability = _temp_column', []);

        Schema::table('students', function(Blueprint $table) {
            $table->dropColumn('_temp_column');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('students', function(Blueprint $table) {
            $table->text('_temp_column')->nullable();
        });

        DB::update('UPDATE students SET _temp_column = availability', []);

        Schema::table('students', function(Blueprint $table) {
            $table->dropColumn('availability');
        });

        Schema::table('students', function(Blueprint $table) {
            $table->enum('availability', ['freelance', 'part_time'])->nullable();
        });

        DB::update('UPDATE students SET availability = _temp_column', []);

        Schema::table('students', function(Blueprint $table) {
            $table->dropColumn('_temp_column');
        });
    }
}
