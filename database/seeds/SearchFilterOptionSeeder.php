<?php

use Illuminate\Database\Seeder;
use App\Models\SearchFilterOptions;

class SearchFilterOptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        SearchFilterOptions::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $json2 = base_path('storage/import_files/attractions_filters_options.json');
        $content2 = json_decode(file_get_contents($json2));

        foreach($content2 as $option){
            if($option->section_id == "subtype"){
                $types = $option->filter_groups[0]->options;
                foreach( $types as  $type){
                    if(isset($type->parent_id)){
                        $parent_id = $type->parent_id;
                    }else{
                        $parent_id = 0;
                    }

                    //insert into database
                    SearchFilterOptions::create([
                        'section_id' => "subtype",
                        'label' => $type->label,
                        'value' => $type->value,
                        'count' => $type->count,
                        'single_select' => $type->single_select,
                        'parent_id' =>  $parent_id ,
                    ]);
                }
            }
        }

     //save combined food options
        $json1 = base_path('storage/import_files/restaurant_filters_options.json');
        $content1 = json_decode(file_get_contents($json1));

        foreach($content1 as $option){
            if($option->section_id == "combined_food"){
                $cuisines = $option->filter_groups[0]->options;
                $parent_id = 0;

                foreach( $cuisines as  $cuisine){
                    //insert into database
                    SearchFilterOptions::create([
                        'section_id' => "combined_food",
                        'label' => $cuisine->label,
                        'value' => $cuisine->value,
                        'count' => $cuisine->count,
                        'single_select' => $cuisine->single_select,
                        'parent_id' =>  $parent_id ,
                    ]);
                }
            }
        }
       

      
    }
}
