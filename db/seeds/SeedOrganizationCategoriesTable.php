<?php

use App\Models\OrganizationCategory;
use Cradle\Seed;

class SeedOrganizationCategoriesTable extends Seed
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
        foreach (['Agriculture', 'Finance', 'Technology', 'Others'] as $categoryName) {
            $category = new OrganizationCategory;
            $category->name = $categoryName;
            $category->save();
        }
    }
}
