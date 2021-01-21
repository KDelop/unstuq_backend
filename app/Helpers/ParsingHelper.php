<?php


if (!function_exists('get_parsed_photos_response')) {
    function get_parsed_photos_response($data)
    {
        $photos = [];
        foreach($data->data as $photo){
                $arr = [];
            if(isset($photo->images->large->url)  && !empty($photo->images->large->url)){
                $arr['photo'] = $photo->images->large->url;
                $arr['caption'] =  $photo->caption;
                $arr['helpful_votes'] = $photo->helpful_votes;
                $arr['published_date'] = $photo->published_date;
                $photos[] = $arr;
            }
        }

        return [
            'photos' => $photos,
            'paging' => $data->paging
        ];
    }
}

if (!function_exists('get_parsed_location_detail')) {
    function get_parsed_location_detail($response)
    {
        $arr = [];
        if(!empty($response)){
            if(isset($response->location_id)){
                $arr['location_id'] = $response->location_id;

            }

            $arr['name'] = $arr['num_reviews'] =  $arr['latitude'] = $arr['longitude'] = '';

            if(isset($response->name)){
                $arr['name'] = $response->name;
            }
            if(isset($response->latitude)){
                $arr['latitude'] = $response->latitude;
            }
            if(isset($response->longitude)){
                $arr['longitude'] = $response->longitude;
            }
            if(isset($response->num_reviews)){
                $arr['num_reviews'] = $response->num_reviews;
            }

            $arr['ranking'] = $arr['rating'] =  $arr['price'] = $arr['description'] = '';
            if(isset($response->ranking)){
                $arr['ranking'] = $response->ranking;
            }
            if(isset($response->rating)){
                $arr['rating'] = $response->rating;
            }
            if(isset($response->price)){
                $arr['price'] = $response->price;
            }
            if(isset($response->description)){
                $arr['description'] = $response->description;
            }

            $arr['web_url'] =  $arr['address'] = $arr['phone'] = '';
            $arr['reviews'] = []; $arr['address_obj'] = new stdClass();
            $arr['working_days'] = $arr['working_time']='';

            if(isset($response->address_obj)){
                $arr['address_obj'] = $response->address_obj;
            }
            $week_array = array('1'=>'Monday','2'=> 'Tuesday','3' => 'Wednesday','4'=>'Thursday','5' => 'Friday', '6' => 'Saturday', '7' =>'Sunday');
           if(isset($response->hours)){
                $week_range_cnt = count($response->hours->week_ranges);
                for($i=0; $i < $week_range_cnt;$i++){
                    if(!empty($response->hours->week_ranges[$i])){
                        $open_minutes = $response->hours->week_ranges[$i][0]->open_time;
                        $open_hours = floor($open_minutes / 60);
                        $open_min = $open_minutes - ($open_hours * 60);
                        $open_12_form = sprintf('%02d',$open_hours).':'. sprintf('%02d',$open_min);
                        $open_time = date("g:i A", strtotime($open_12_form));

                        $close_minutes = $response->hours->week_ranges[$i][0]->close_time;
                        $close_hours = floor($close_minutes / 60);
                        $close_min = $close_minutes - ($close_hours * 60);
                        $close_12_form = sprintf('%02d',$close_hours).':'. sprintf('%02d',$close_min);
                        $close_time = date("g:i A", strtotime($close_12_form));
                        $arr['working_days'] = 'Monday - '. $week_array[count($response->hours->week_ranges)];

                        $arr['working_time'] = $open_time.' - '. $close_time;   
                        break;
                    }else{
                        continue;                     
                    }
                }
         

            }

            //hours logic for week days

           /* foreach($response->hours->week_ranges as $week_days){

            }*/

                /*   $week_count = 0;
                   $week = [];
                  // if(isset($response->hours) && !empty($response->hours)){
                       foreach($response->hours->week_ranges as $week_days){
                           if(isset($week_days[0])){
                               // $week[$week_count] = $week_days[0]->open_time."-".$week_days[0]->close_time;
                               $week[$week_count]['open'] = $week_days[0]->open_time;
                               $week[$week_count]['close'] = $week_days[0]->close_time;
                           }
                           $week_count++;
                       }
                   }/*else{
                       continue;
                   }*/

            if(isset($response->timezone)){
                $arr['timezone'] = $response->timezone;
            }


            if(isset($response->web_url)){
                $arr['web_url'] = $response->web_url;
            }
            if(isset($response->address)){
                $arr['address'] = $response->address;
            }
             if(isset($response->reviews)){
                 $arr['reviews'] = $response->reviews;
             }

            if(isset($response->phone)){
                $arr['phone'] = $response->phone;
            }

            $arr['website'] = $arr['email'] =  $arr['menu_web_url'] = $arr['cuisine'] = '';
            if(isset($response->website)){
                $arr['website'] = $response->website;
            }
            if(isset($response->email)){
                $arr['email'] = $response->email;
            }
            if(isset($response->menu_web_url)){
                $arr['menu_web_url'] = $response->menu_web_url;
            }

            if(isset($response->cuisine)){
                foreach($response->cuisine as $cuisine){
                    $arr['cuisine'] = $cuisine->name.",";
                }
                $arr['cuisine'] = rtrim( $arr['cuisine'],',');
            }
            $arr['distance'] = $arr['distance_string'] =  $arr['bearing'] = $arr['is_closed'] = '';
            $arr['open_now_text'] = $arr['is_long_closed'] =  $arr['price_level'] = $arr['price'] = $arr['description'] = '';
            $arr['timezone'] = '';

            if(isset($response->distance)){
                $arr['distance'] = $response->distance;
            }
            if(isset($response->distance_string)){
                $arr['distance_string'] = $response->distance_string;
            }
            if(isset($response->bearing)){
                $arr['bearing'] = $response->bearing;
            }

            if(isset($response->is_closed)){
                $arr['is_closed'] = $response->is_closed;
            }
            if(isset($response->open_now_text)){
                $arr['open_now_text'] = $response->open_now_text;
            }
            if(isset($response->is_long_closed)){
                $arr['is_long_closed'] = $response->is_long_closed;
            }

            if(isset($response->price_level)){
                $arr['price_level'] = $response->price_level;
            }
            if(isset($response->price)){
                $arr['price'] = $response->price;
            }
            if(isset($response->description)){
                $arr['description'] = $response->description;
            }

            $arr['photo_count'] = $arr['owners_top_reasons'] = '';
            if(isset($response->photo_count)){
                $arr['photo_count'] = $response->photo_count;
            }
            if(isset($response->owners_top_reasons)){
                $arr['owners_top_reasons'] = $response->owners_top_reasons;
            }

            $arr['location_string'] = $arr['photo'] = '';
            if(isset($response->location_string)){
                $arr['location_string'] = $response->location_string;
            }

            if(isset($response->photo->images->large->url)  && !empty($response->photo->images->large->url)){
                $arr['photo'] = $response->photo->images->large->url;
            }

        }

        return $arr;

    }
}



if (!function_exists('parse_tips_data')) {
    function parse_tips_data($data)
    {
        if(!empty($data) && property_exists($data,'data')){

            $tips = [];
            foreach($data->data as $tip){
                $arr = [];

                $arr['username'] = $tip->user->username;
                $arr['type'] =  $tip->type;
                $arr['text'] = $tip->text;
                $arr['rating'] = $tip->rating;
                $tips[] = $arr;
            }

            return [
                'tips' => $tips,
                'paging' => $data->paging
            ];
        }else{
            return [
                'tips' => [],
                'paging' => new stdClass()
            ];
        }

    }
}

if (!function_exists('get_parsed_search_response')) {
    function get_parsed_search_response($data)
    {
        $searched_entities = json_decode($data->results);
        $parsed_data = [];
        return $searched_entities; //already parsed info saved in database

        // foreach($searched_entities as $entity){
        //     $parsed_data[] = get_parsed_location_detail($entity);
        // }
        // return $parsed_data;
    }
}

if (!function_exists('get_parsed_search_matched_response')) {
    function get_parsed_search_matched_response($data)
    {
        $matched_entity = '';
        $matched_entity_id = $data->matched_entity_id;
        //search info - image, rating, address, lat, long, location name
        $searched_entities = json_decode($data->results)->results;

        foreach($searched_entities as $entity){
            if($entity->location_id == $matched_entity_id){
                $matched_entity = $entity;
            }
        }

        $matched_entity = get_parsed_location_detail($matched_entity);

        return $matched_entity;
    }
}
