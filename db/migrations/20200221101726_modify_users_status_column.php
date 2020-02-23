<?php

use Cradle\Migration;
use Illuminate\Database\Schema\Blueprint;

class ModifyUsersStatusColumn extends Migration
{
    public function up()
    {
        $this->schema->table('users', function (Blueprint $table) {
            $table->enum('_temp_column', ['active', 'banned']);
        });

        $this->db->connection()->getPdo()->exec("UPDATE users SET _temp_column = status");

        $this->schema->table('users', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        $this->schema->table('users', function (Blueprint $table) {
            $table->enum('status', ['active', 'banned', 'pending'])->default('pending');
        });

        $this->db->connection()->getPdo()->exec("UPDATE users SET status = _temp_column");

        $this->schema->table('users', function (Blueprint $table) {
            $table->dropColumn('_temp_column');
        });
    }

    public function down()
    {
        $this->schema->table('users', function (Blueprint $table) {
            $table->enum('_temp_column', ['active', 'banned', 'pending']);
        });

        $this->db->connection()->getPdo()->exec("UPDATE users SET _temp_column = status");
        $this->db->connection()->getPdo()->exec("UPDATE users SET _temp_column = 'active' WHERE _temp_column = 'pending'");

        $this->schema->table('users', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        $this->schema->table('users', function (Blueprint $table) {
            $table->enum('status', ['active', 'banned'])->default('active');
        });

        $this->db->connection()->getPdo()->exec("UPDATE users SET status = _temp_column");

        $this->schema->table('users', function (Blueprint $table) {
            $table->dropColumn('_temp_column');
        });
    }
}
