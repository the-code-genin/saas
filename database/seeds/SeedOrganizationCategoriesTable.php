<?php

use App\Models\OrganizationCategory;
use Illuminate\Database\Seeder;

class SeedOrganizationCategoriesTable extends Seeder
{
    public function run()
    {
        foreach (['Agriculture', 'Finance', 'Technology', 'Others'] as $categoryName) {
            $category = new OrganizationCategory;
            $category->name = $categoryName;
            $category->save();
        }
    }
}
