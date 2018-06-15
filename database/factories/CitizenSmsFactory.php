<?php

use Carbon\Carbon;
use Faker\Generator as Faker;

$factory->define(App\Argo\CitizenSMS::class, function (Faker $faker) {
    $phones = [
        $faker->e164PhoneNumber,
        '0987654321',
        '0912345678',
        '0918273645',
        '0981726354'
    ];

    return [
        'message' => $faker->realText(50),
        'phone_number' => $faker->randomElement($phones),
        'is_read' => $faker->boolean,
        'created_at' => $faker->dateTimeBetween($startDate = '-1 years', $endDate = 'now', $timezone = null),
        'updated_at' => Carbon::now(),
    ];
});
