<?php

use App\Models\StudentProfileView;
use Illuminate\Database\Seeder;

class StudentProfileViewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(StudentProfileView::class, 100)->make()->each(function ($view) {
            $view->student_id = 3;
            $view->organization_id = 1;
            $view->save();
        });
    }
}
