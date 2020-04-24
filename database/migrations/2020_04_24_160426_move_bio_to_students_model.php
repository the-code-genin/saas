<?php

use App\Models\Student;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MoveBioToStudentsModel extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('students', function (Blueprint $table) {
            $table->text('bio')->nullable();
        });

        DB::update('UPDATE students SET bio = (SELECT bio FROM users WHERE users.userable_id = students.id AND users.userable_type = ?)', [Student::class]);

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('bio');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('bio')->nullable();
        });

        DB::update('UPDATE users SET bio = (SELECT bio FROM students WHERE users.userable_id = students.id AND users.userable_type = ?)', [Student::class]);

        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn('bio');
        });
    }
}
