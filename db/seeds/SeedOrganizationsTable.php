<?php

use App\Models\User;
use App\Models\Organization;
use App\Models\OrganizationCategory;
use Cradle\Seed;

class SeedOrganizationsTable extends Seed
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
        $organization = new Organization;
        $organization->name = $this->faker->name;
        $organization->description = $this->faker->sentence;
        $organization->category_id = OrganizationCategory::inRandomOrder()->first()->id;
        $organization->save();

        $user = new User;
        $user->email = $this->faker->email;
        $user->password = password_hash('password', PASSWORD_DEFAULT);
        $user->address = $this->faker->address;
        $user->phone_number = $this->faker->phoneNumber;

        $user->userable()->associate($organization);
        $user->save();
    }
}
