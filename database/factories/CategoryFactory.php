<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Product;
use Faker\Generator as Faker;

$factory->define(Product::class, function (Faker $faker) {
    return [
        'name'=>$faker-> word(),
        'slug'=>$faker->slug(),
        'is_active'=>$faker->boolean(),
    ];
});
