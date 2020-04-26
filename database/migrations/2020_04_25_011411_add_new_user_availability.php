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
            $table->enum('_temp_column', ['freelance', 'full_time', 'part_time', 'week_days', 'week_ends'])
                ->default('freelance');
        });

        DB::update('UPDATE students SET _temp_column = availability', []);

        Schema::table('students', function(Blueprint $table) {
            $table->dropColumn('availability');
        });

        Schema::table('students', function(Blueprint $table) {
            $table->enum('availability', ['freelance', 'full_time', 'part_time', 'week_days', 'week_ends'])->default('freelance');
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
            $table->enum('_temp_column', ['freelance', 'part_time'])->default('freelance');
        });

        DB::update('UPDATE students SET _temp_column = availability WHERE availability IN (\'freelance\', \'part_time\')', []);

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
