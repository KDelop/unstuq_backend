<?php

use Illuminate\Database\Seeder;
use App\Models\StreamingProvider;

class StreamingProviderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        StreamingProvider::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $sql = base_path('storage/import_files/streaming_providers.sql');

        //collect contents and pass to DB::unprepared
        DB::unprepared(file_get_contents($sql));
        // DB::statement(file_get_contents($sql));
        
        $this->command->info('Streaming provider seeded!');
    }
}
