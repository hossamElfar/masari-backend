<?php

use Illuminate\Database\Seeder;

class TimingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Timing::class, 10)->create();
    }
}
