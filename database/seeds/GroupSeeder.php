<?php

use Illuminate\Database\Seeder;
use App\Models\UserGroup;
use App\Models\UserGroupMember;
use App\Models\User;

class GroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        UserGroup::truncate();
        UserGroupMember::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $faker = \Faker\Factory::create();

        $users = User::where('status','1')->get();
        $users_array = $users->toArray();

        $count = 0;
        foreach($users  as $user) {
            $count++;
            $group = UserGroup::create([
                'group_name' => $faker->name,
                'group_icon' => $faker->imageUrl($width = 200, $height = 200),
                'user_id' => $user->id,
                'created_at' => gmdate('Y-m-d H:i:s')
            ]);
           
            //add group members
            $member_count = 0;
            $group->members()->attach($user->id);

            shuffle($users_array);
            foreach($users_array  as $member) {
                $member_count++;
                if(!$group->members->contains($member['id'])){
                    $group->members()->attach($member['id']);
                }
                if($member_count == 4){
                    break;
                }
            }
           
            if($count == 4){
                break;
            }

        }

    }
}
