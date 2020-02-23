<?php

use Cradle\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddUserVerifiedField extends Migration
{
    public function up()
    {
        $this->schema->table('users', function (Blueprint $table) {
            $table->enum('verified', ['true', 'false'])->default('false');
        });
    }

    public function down()
    {
        $this->schema->table('users', function (Blueprint $table) {
            $table->dropColumn('verified');
        });
    }
}
