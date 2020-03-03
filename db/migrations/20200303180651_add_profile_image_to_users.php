<?php

use Cradle\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddProfileImageToUsers extends Migration
{
    public function up()
    {
        $this->schema->table('users', function (Blueprint $table) {
            $table->text('profile_image')->nullable();
        });

        $this->schema->table('students', function (Blueprint $table) {
            $table->text('cv')->nullable();
        });
    }

    public function down()
    {
        $this->schema->table('users', function (Blueprint $table) {
            $table->dropColumn('profile_image');
        });

        $this->schema->table('students', function (Blueprint $table) {
            $table->dropColumn('cv');
        });
    }
}
