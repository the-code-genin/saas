<?php

use App\Models\Student;
use Cradle\Seed;
use App\Models\User;

class SeedStudentsTable extends Seed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * http://docs.phinx.org/en/latest/seeding.html
     */
    public function run()
    {
        $student = new Student;
        $student->full_name = $this->faker->name;
        $student->availability = $this->faker->randomElement(['freelance', 'part_time']);
        $student->hourly_rate = $this->faker->randomFloat(2, 5.0, 100);
        $student->save();

        $user = new User;
        $user->email = $this->faker->email;
        $user->password = password_hash('password', PASSWORD_DEFAULT);
        $user->address = $this->faker->address;
        $user->phone_number = $this->faker->phoneNumber;

        $user->userable()->associate($student);
        $user->save();
    }
}
