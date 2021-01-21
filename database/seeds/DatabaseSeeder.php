<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call('GenreSeeder');
        $this->call('StreamingProviderSeeder');
        $this->call('UserSeeder');
        $this->call('GroupSeeder');
        $this->call('SearchFilterOptionSeeder');
    }
}
