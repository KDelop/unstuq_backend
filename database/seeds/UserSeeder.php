<?php

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        User::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $faker = \Faker\Factory::create();
        $MIN_SESSION_ID = 1000000000;
        $MAX_SESSION_ID = 9999999999;
        
        $test_numers = [
            '8879676620',
            '8879676486',
            '8319853214',
            '9730727758',
            '9575828473'
        ];

        $test_names = [
            'neha bhole',
            'mahesh bharambe',
            'piyush bhole',
            'bharat bharambe',
            'alka bhole'
        ];

        for($i = 0; $i < 5; $i++) {
            $randId = mt_rand($MIN_SESSION_ID, $MAX_SESSION_ID);
            $access_code = strtoupper(get_random_strings(6));

            $user = User::create([
                // 'phone' => "+91".$randId,
                'phone' => "+91".$test_numers[$i],
                // 'name' => $faker->name,
                'name' => $test_names[$i],
                'email' => $faker->email,
                'avatar' => $faker->imageUrl($width = 200, $height = 200),
                'user_type' => 1,
                'status' => 1,
                'access_code' => $access_code,
                'created_at' => gmdate('Y-m-d H:i:s')
            ]);

            //user device entry

        }
    }
}
