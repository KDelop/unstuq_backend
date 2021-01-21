<?php

if (!function_exists('callAPI')) {
    function callAPI( $method, $url, $data , $headers = ['Content-Type: application/json'] ){
        $curl = curl_init();
        switch ($method){
           case "POST":
              curl_setopt($curl, CURLOPT_POST, 1);
              if ($data)
                 curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
              break;
           case "PUT":
              curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
              if ($data)
                 curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
              break;
           default:
              if ($data)
                 $url = sprintf("%s?%s", $url, http_build_query($data));
        }
		
        // OPTIONS:
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        // EXECUTE:
        $result = curl_exec($curl);
        if(!$result){die("Connection Failure");}
        curl_close($curl);

        return json_decode($result);
     }
}

function search_and_parse( $url, $data , $search_day, $search_in_mins, $page = 0 , $previous_resp = [] ){
	
    $limit = 30;
    $max_results = config('constant.max_search_results'); //will be 20

    $host = env('SEARCH_API_HOST');
    $key = env('SEARCH_API_KEY');

    $headers = [ 'Content-Type: application/json',
                    'x-rapidapi-host: '.$host,
                    'x-rapidapi-key: '.$key ];

    $method = "GET";
    if(!isset($data['offset'])){
        $data['offset'] = $page*$limit;
    }else{
        $data['offset'] = $data['offset'] + $page*$limit;
    }
	
	if($data["type"] == "restaurants")
	{
		$realData = Array();
		$realData["latitude"] = $data["latitude"];
		$realData["longitude"] = $data["longitude"];
		$realData["lunit"] = $data["lunit"];
		$realData["distance"] = $data["distance"];
		$realData["limit"] = $data["limit"];
		$realData["currency"] = $data["currency"];
		$realData["lang"] = $data["lang"];
		$realData["offset"] = $data["offset"];
        
        if($data["combined_food"] == "10597" || $data["combined_food"] == "10598" || $data["combined_food"] == "10599" || $data["combined_food"] == "10606")
        {
            $realData["restaurant_mealtype"] = $data["combined_food"];
            $realData["min_rating"] = 3;
            $boundingBoxLink = "https://api.unstuq.com/geo/index.php?lat=".$data["latitude"]."&long=".$data["longitude"]."&radius=".$data["distance"]."&unit=".$data["lunit"];
            $boundingBox = json_decode(file_get_contents($boundingBoxLink),true);
            $realData["tr_latitude"] = $boundingBox["maxLatitude"];
            $realData["tr_longitude"] = $boundingBox["maxLongitude"];
            $realData["bl_latitude"] = $boundingBox["minLatitude"];
            $realData["bl_longitude"] = $boundingBox["minLongitude"];
        }
        else
        {
            $realData["combined_food"] = $data["combined_food"];
        }
		
		$results = callAPI($method, $url, $realData , $headers);

		$resp = get_parsed_location_response($results, $search_day, $search_in_mins,$data["type"]);
		$resp = array_merge($resp, $previous_resp);
		

        /*if(count($resp) <  $max_results && json_decode($results, true)["paging"]["total_results"] > $max_results)
		{
            //call api again for next 30 records
           	$new_page = $page + 1;
           	$new_resp = search_and_parse( $url, $data , $search_day, $search_in_mins, $new_page, $resp );
         	return $new_resp;
        }
		else
		{*/
			$extra = 0 ;
            $actual_count = count($resp);
            if( $actual_count >  $max_results){
                $extra = $actual_count - $max_results;
                //remove extra results
                $newresp = array_splice($resp, 0, "-".$extra);
            }else{
                $newresp = $resp;
            }
            $offset = $page * $limit - $extra;

            $total_results = count($newresp);
            shuffle($newresp);//to get random order everytime for same search

            return  [
                'results' => $newresp,
                'debug' => [
                    'page' =>  $page,
                    'actual_count' => $actual_count,
                    'extra' =>  $extra ,
                    'day' =>  $search_day,
                    'search_in_mins' => $search_in_mins,
                ],
                'result_count' => $total_results,
                'offset' => $offset,
            ];
        //}
	}
	else if($data["type"] == "attractions")
	{
		$realData = Array();
		$realData["lunit"] = $data["lunit"];
		$realData["distance"] = $data["distance"];
		$realData["limit"] = $data["limit"];
		$realData["currency"] = $data["currency"];
		$realData["lang"] = $data["lang"];
		$realData["offset"] = $data["offset"];
		$realData["subcategory"] = $data["combined_food"];
		$realData["min_rating"] = 3;
		$boundingBoxLink = "https://api.unstuq.com/geo/index.php?lat=".$data["latitude"]."&long=".$data["longitude"]."&radius=".$data["distance"]."&unit=".$data["lunit"];
		$boundingBox = json_decode(file_get_contents($boundingBoxLink),true);
		$realData["tr_latitude"] = $boundingBox["maxLatitude"];
		$realData["tr_longitude"] = $boundingBox["maxLongitude"];
		$realData["bl_latitude"] = $boundingBox["minLatitude"];
		$realData["bl_longitude"] = $boundingBox["minLongitude"];
		
		$results = callAPI($method, $url, $realData , $headers);

		$resp = get_parsed_location_response($results, $search_day, $search_in_mins,$data["type"]);
		$resp = array_merge($resp, $previous_resp);

        //if(count($resp) <  $max_results)
		//{
            //call api again for next 30 records
        //    $new_page = $page + 1;
         //   $new_resp = search_and_parse( $url, $data , $search_day, $search_in_mins, $new_page, $resp );
        //  	return $new_resp;
        //}
		//else
		//{
			$extra = 0 ;
            $actual_count = count($resp);
            if( $actual_count >  $max_results){
                $extra = $actual_count - $max_results;
                //remove extra results
                $newresp = array_splice($resp, 0, "-".$extra);
            }else{
                $newresp = $resp;
            }
            $offset = $page * $limit - $extra;

            $total_results = count($newresp);
            shuffle($newresp);//to get random order everytime for same search

            return  [
                'results' => $newresp,
                'debug' => [
                    'page' =>  $page,
                    'actual_count' => $actual_count,
                    'extra' =>  $extra ,
                    'day' =>  $search_day,
                    'search_in_mins' => $search_in_mins,
                ],
                'result_count' => $total_results,
                'offset' => $offset,
            ];
        //}
	}
}


if (!function_exists('get_parsed_location_response')) {
    function get_parsed_location_response( $results, $search_day,$search_time_in_mins, $type)
    {
        $resp = [];
        foreach($results->data as $response){
            $arr = [];

            $required = array('location_id', 'name', 'photo','hours');

            $arr['location_id'] = $arr['name'] = $arr['num_reviews'] =  $arr['location_string'] = $arr['photo'] = '';

            $arr['location_id'] = $response->location_id;
            if(isset($response->name) && !empty($response->name)){
                $arr['name'] = $response->name;
            }else{
                continue;
            }

            $arr['latitude'] = $arr['longitude'] = '';
            if(isset($response->latitude)){
                $arr['latitude'] = $response->latitude;
            }
            if(isset($response->longitude)){
                $arr['longitude'] = $response->longitude;
            }

            if(isset($response->num_reviews)){
                $arr['num_reviews'] = $response->num_reviews;
            }
            if(isset($response->location_string)){
                $arr['location_string'] = $response->location_string;
            }
            if(isset($response->photo->images->large->url)  && !empty($response->photo->images->large->url)){
                $arr['photo'] = $response->photo->images->large->url;
            }else{
                $arr['photo'] = "https://unstuq.com/restaurant.jpg";
				if($type == "attractions")
				{
					$arr['photo'] = "https://unstuq.com/attractions.jpg";
				}
            }

            $arr['description'] = $arr['web_url'] = $arr['write_review'] =  $arr['address'] = $arr['cuisine'] = '';
            if(isset($response->description) && $response->description !=""){
                $arr['description'] = $response->description;
            }
            else
            {
                continue;
            }
            if(isset($response->web_url)){
                $arr['web_url'] = $response->web_url;
            }
            if(isset($response->write_review)){
                $arr['write_review'] = $response->write_review;
            }
            if(isset($response->address) && $response->address != ""){
                $arr['address'] = $response->address;
            }
            else
            {
                continue;
            }
            if(isset($response->cuisine)){
                foreach($response->cuisine as $cuisine){
                    $arr['cuisine'] = $cuisine->name.",";
                }
                $arr['cuisine'] = rtrim( $arr['cuisine'],',');
            }

                $week_count = 0;
                $week = [];
            if(isset($response->hours) && !empty($response->hours)){
                foreach($response->hours->week_ranges as $week_days){
                    if(isset($week_days[0])){
                        $week[$week_count]['open'] = $week_days[0]->open_time;
                        $week[$week_count]['close'] = $week_days[0]->close_time;
                    }
                        $week_count++;
                }
            }

            $check_open = 0;
            
            if(isset($week[$search_day]['open']) && isset($week[$search_day]['close'])){
                $check_open = 1;
            }
            
            $arr['ranking'] = $arr['distance_string'] = $arr['rating'] =   $arr['price'] = '';

            if(isset($response->ranking)){
                $arr['ranking'] = $response->ranking;
            }
            if(isset($response->distance_string)){
                $arr['distance_string'] = $response->distance_string;
            }
            if(isset($response->rating)){
                $arr['rating'] = $response->rating;
            }
          
            if(isset($response->price)){
                $arr['price'] = $response->price;
            }

            $arr['phone'] = $arr['website'] = '';

            if(isset($response->phone)){
                $arr['phone'] = $response->phone;
            }
            if(isset($response->website)){
                $arr['website'] = $response->website;
            }

            $resp[] = $arr;
        }

       return $resp;
    }

    function search_and_parse_only_data( $url, $data , $page = 0 , $previous_resp = [] ){

        $limit = 30;
        $max_results = config('constant.max_search_results'); //will be 20

        $host = env('SEARCH_API_HOST');
        $key = env('SEARCH_API_KEY');

        $headers = [ 'Content-Type: application/json',
            'x-rapidapi-host: '.$host,
            'x-rapidapi-key: '.$key ];

        $method = "GET";
        if(!isset($data['offset'])){
            $data['offset'] = $page*$limit;
        }else{
            $data['offset'] = $data['offset'] + $page*$limit;
        }
        $results = callAPI($method, $url, $data , $headers);
        if($results){
            return  [
                'results' => $results
            ];
        }

    }

}