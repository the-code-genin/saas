<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveUserVerifiedAttribute extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('verified');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('verified', ['true', 'false'])->default('false');
        });
    }
}
