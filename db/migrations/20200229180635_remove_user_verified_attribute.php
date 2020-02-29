<?php

use Cradle\Migration;
use Illuminate\Database\Schema\Blueprint;

class RemoveUserVerifiedAttribute extends Migration
{
    public function up()
    {
        $this->schema->table('users', function (Blueprint $table) {
            $table->dropColumn('verified');
        });
    }

    public function down()
    {
        $this->schema->table('users', function (Blueprint $table) {
            $table->enum('verified', ['true', 'false'])->default('false');
        });
    }
}
