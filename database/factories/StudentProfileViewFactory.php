<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Carbon\Carbon;
use Faker\Generator as Faker;
use App\Models\StudentProfileView;

$factory->define(StudentProfileView::class, function (Faker $faker) {
    $now = Carbon::now();
    $remainingMonths = 12 - $now->month;
    return [
        'created_at' => $faker->dateTimeBetween("- {$now->month} months", "$remainingMonths  months")
    ];
});
