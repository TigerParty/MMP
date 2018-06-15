<?php

use Carbon\Carbon;
use Faker\Generator as Faker;

$factory->define(\App\Argo\ReportCitizen::class, function (Faker $faker) {
    $location = $faker->randomElement([
        [
            "lat" => null,
            "lng" => null,
        ],
        [
            "lat" => $faker->latitude,
            "lng" => $faker->longitude,
        ],
    ]);

    return [
        "email" => $faker->freeEmail,
        "phone" => $faker->phoneNumber,
        "comment" => $faker->realText(30),
        "lat" => $location['lat'],
        "lng" => $location['lng'],
        "is_read" => $faker->boolean,
        "created_at" => $faker->dateTimeBetween($startDate = '-1 years', $endDate = 'now', $timezone = null),
        "updated_at" => Carbon::now(),
    ];
});
