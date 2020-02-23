<?php

use Cradle\Migration;
use Illuminate\Database\Schema\Blueprint;

class ModifyUsersStatusColumn extends Migration
{
    public function up()
    {
        $this->db->connection()->getPdo()
            ->exec("ALTER TABLE `users` MODIFY COLUMN `status` ENUM('pending','active','banned') DEFAULT 'pending' NOT NULL;");
    }

    public function down()
    {
        $this->db->table('users')->where('status', 'pending')->update(['status' => 'active']);
        $this->db->connection()->getPdo()
            ->exec("ALTER TABLE `users` MODIFY COLUMN `status` ENUM('active','banned') DEFAULT 'active' NOT NULL;");
    }
}
