<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
    ];
});

$factory->define(App\News::class, function (Faker\Generator $faker) {
    return [
        'content' => $faker->name,
        'title' => $faker->title,
        'verified' => true,
        'user_id' => 1
    ];
});
$factory->define(App\Questionnaire::class, function (Faker\Generator $faker) {
    $random = ['ar','en'];
    return [
        'name' => $faker->name,
        'no_of_questions' => 20,
        'language'=>$random[array_rand($random, 1)]
    ];
});
$factory->define(App\Question::class, function (Faker\Generator $faker) {
    return [
        'question_content' => $faker->sentence,
        'category' => $faker->title,
        'no_of_answers' => 4
    ];
});
$factory->define(App\Answer::class, function (Faker\Generator $faker) {
    return [
        'answer_content' => $faker->sentence,
        'points' => $faker->numberBetween(1, 10)

    ];
});
$factory->define(App\Video::class, function (Faker\Generator $faker) {
    return [
        'title' => $faker->title,
        'note' => $faker->sentence,
        'link' => "http://www." . $faker->word . ".com",
        'verified' => true,
        'user_id' => 1
    ];
});
$factory->define(App\Link::class, function (Faker\Generator $faker) {
    return [
        'title' => $faker->title,
        'description' => $faker->sentence,
        'link' => "http://www." . $faker->word . ".com",
        'verified' => true,
        'user_id' => 1
    ];
});

$factory->define(App\Program::class, function (Faker\Generator $faker) {
    return [
        'title' => $faker->title,
        'description' => $faker->sentence,
        'from' => $faker->date(),
        'to' => $faker->date(),
        'verified' => true,
        'user_id' => 1
    ];
});

$factory->define(App\Event::class, function (Faker\Generator $faker) {
    return [
        'title' => $faker->title,
        'description' => $faker->sentence,
        'venue' => $faker->city,
        'start' => \Carbon\Carbon::now(),
        'end' => \Carbon\Carbon::now()->addHours(3),
        'date' => $faker->date(),
        'verified' => true,
        'user_id' => 1
    ];
});

$factory->define(App\Field::class, function (Faker\Generator $faker) {
    $random = ['sports', 'tech', 'politics'];
    return [
        'field_name' => $random[array_rand($random, 1)]
    ];
});

$factory->define(App\Q::class, function (Faker\Generator $faker) {
    return [
        'question' => $faker->sentence() . ' ?',
        'user_id' => 1
    ];
});
$factory->define(App\A::class, function (Faker\Generator $faker) {
    return [
        'answer' => $faker->sentence(),
        'verified' => true,
        'user_id' => 1
    ];
});

$factory->define(App\Timing::class, function (Faker\Generator $faker) {
    return [
        'timing' => $faker->dateTimeBetween(\Carbon\Carbon::now(), \Carbon\Carbon::now()->addMonth()),
        'reserved' => false,
        'user_id' => 1
    ];
});


