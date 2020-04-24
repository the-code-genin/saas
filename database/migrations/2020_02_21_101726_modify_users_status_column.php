<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ModifyUsersStatusColumn extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('_temp_column', ['active', 'banned'])->nullable();
        });

        DB::update("UPDATE users SET _temp_column = status");

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->enum('status', ['active', 'banned', 'pending'])->default('pending');
        });


        DB::update("UPDATE users SET status = _temp_column");

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('_temp_column');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('_temp_column', ['active', 'banned', 'pending'])->nullable();
        });

        DB::update("UPDATE users SET _temp_column = status");
        DB::update("UPDATE users SET _temp_column = 'active' WHERE _temp_column = 'pending'");

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->enum('status', ['active', 'banned'])->default('active');
        });

        DB::update("UPDATE users SET status = _temp_column");

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('_temp_column');
        });
    }
}
