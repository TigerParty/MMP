<?php

use Carbon\Carbon;
use Faker\Generator as Faker;

$factory->define(App\Argo\CitizenSMSReply::class, function (Faker $faker) {
    return [
        'message' => $faker->realText(50),
        'created_at' => $faker->dateTimeBetween($startDate = '-1 years', $endDate = 'now', $timezone = null),
        'updated_at' => Carbon::now(),
    ];
});
