<?php

use Twilio\Rest\Client;
use Illuminate\Support\Facades\Log;


if (!function_exists('isJson')) {
    function isJson($string) {
         json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }
}


if (!function_exists('get_search_users_attending_event')) {
    function get_search_users_attending_event($search, $skippedUsers){

        $groups = $search->groups;
        $unique_members = [];

        foreach($groups as $group){
            $members = $group->members;
            foreach($members as $member){
                if(find_key_value( $skippedUsers, "user_id", $member->id)){
                    //skip this user
                }else{
                    $unique_members[] = $member->toArray();
                }
            }
        }

        return  $unique_members;
    }
}

if (!function_exists('get_message_text')) {
    function get_message_text($message_name, $data) {
       $message = Config::get("constant.".$message_name);
       if(!empty($data)){
           foreach($data as $key => $value){
               $message = str_replace(":".$key,$value,$message);
           }
       }
       return $message;
    }
}

if (!function_exists('find_key_value')) {
    function find_key_value($array, $key, $val) {
        foreach ($array as $item)
            if (isset($item[$key]) && $item[$key] == $val)
                return true;
        return false;
    }
}

if (!function_exists('find_object_key_value')) {
    function find_object_key_value($array, $key, $val) {
        foreach ($array as $item)

        if (isset($item->$key) && $item->$key == $val)
                return true;
      /*  if (isset($item[$key]) && $item[$key] == $val)
            return true;*/

        return false;
    }
}

if (!function_exists('find_object_key_value_array')) {
    function find_object_key_value_array($array, $key, $val) {
        foreach ($array as $item)

        if (isset($item[$key]) && $item[$key] == $val)
            return true;

        return false;
    }
}


if (!function_exists('find_object_key_value_object')) {
    function find_object_key_value_object($array, $key, $val) {
        foreach ($array as $item)

        if (isset($item->$key) && $item->$key == $val)
                return true;

        return false;
    }
}


if (!function_exists('error_reponse_handler')) {
    function error_reponse_handler($e,$function_name = ''){
        $route_name = \Route::getCurrentRoute()->getActionName();
        $route = explode("Api", $route_name);
        $route_path = "Api".$route[1];
        Log::info($route_path." : ".$e->getMessage());

        return [
            'response' => ['status' => false ,
                'message' => 'Something Went Wrong',
                'error' => $e->getMessage()],
            'status_code' => 500
             ];

        // return response()->json(['status' => false ,
        //         'message' => 'Something Went Wrong',
        //         'error' => $e->getMessage()],400)->send();

    }
}

if (!function_exists('sendMessage')) {
    function sendMessage($ids,$message,$type)
    {
        if($type == "pending")
        {
            $content = '{"alert":{
                                    "title":"UnstuQ Notifications",
                                    "text":"'.$message.'",
                                    "ios":{
                                            "sound":"default"
                                        },
                                    "android":{
                                        "sound":"default"
                                    }
                                }
                        }';
        }
        else if($type == "match")
        {
           $content = '{"alert":{
                                    "title":"Match Found",
                                    "text":"'.$message.'",
                                    "ios":{
                                            "sound":"default"
                                          },
                                    "android":{
                                        "sound":"default"
                                    }
                                }
                        }';
        }
        else if($type == "experience")
        {
           $content = '{"alert":{
                                    "title":"An Experience was shared with you",
                                    "text":"'.$message.'",
                                    "ios":{
                                            "sound":"default"
                                          },
                                    "android":{
                                        "sound":"default"
                                    }
                                }
                        }';
        }
        else if($type == "leave_experience")
        {
           $content = '{"alert":{
                                    "title":"Experience Update",
                                    "text":"'.$message.'",
                                    "ios":{
                                            "sound":"default"
                                          },
                                    "android":{
                                        "sound":"default"
                                    }
                                }
                        }';
        }
        else if($type == "delete_experience")
        {
           $content = '{"alert":{
                                    "title":"Experience Update",
                                    "text":"'.$message.'",
                                    "ios":{
                                            "sound":"default"
                                          },
                                    "android":{
                                        "sound":"default"
                                    }
                                }
                        }';
        }

        $file = 'pushNotifications.txt';

        if(!is_file($file)){
            $contents = $ids."\n".$message."\n".$type."\n-------------------\n";           // Some simple example content.
            file_put_contents($file, $contents);     // Save our content to the file.
        }

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "https://management-api.wonderpush.com/v1/deliveries");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array(
            'accessToken' => "MWZmNzZmMGVlNTliZWMzOGQxMTA3ZTkxZjQ2MGU4ZTJiZmJkZmY0OGNjZTgyZjA1YmYzYzFiNGU5MWUxMGUzOA",
            'targetInstallationIds' => $ids,
            'notification' => $content
        )));

        $rawResponse = curl_exec($ch);

        if (curl_errno($ch)) {

            echo 'Error: ' . curl_error($ch);

        } else {

            $response = json_decode($rawResponse, true);
            if (isset($response['success']) && $response['success'] === true) {
                return $rawResponse;
            } else if (isset($response['error']['status'])
                    && isset($response['error']['code'])
                    && isset($response['error']['message'])) {
                echo 'Error ' . $response['error']['status']
                . ' code ' . $response['error']['code']
                . ': ' . $response['error']['message'];
            } else {
                echo 'Error: ' . $rawResponse;
            }

        }

        curl_close($ch);
    }}

if (!function_exists('get_parsed_validation_error_response')) {
    function get_parsed_validation_error_response($validator)
    {
        $errors =  $validator->errors()->all();

        $message = '';
        $count = 0;
        $totalCount = count($errors);
        if($totalCount == 1){
            $message .= trim($errors[0]);
        }else{
            foreach($errors as $key => $error){
                $count++;
                $message .= trim($error);
                    if($count < $totalCount){
                        $message .= ", ";
                    }
           }
        }

        $response = [
            'status' => false,
            'message' => $message
            // 'message' => 'Validation Failed',
            // 'data' => [
            //     'error' => $validator->errors()
            // ]
        ];

       return $response;
    }
}

if (!function_exists('twilio_send_sms')) {
    function twilio_send_sms($message, $recipients)
    {
        
		//echo $response;
		return ['status' => 1, 'message' => "Send sms successfully"];
    }
}

if (!function_exists('send_access_code')) {
    function send_access_code($access_code, $recipients)
    {
        $curl = curl_init();
		$url = "https://rest.nexmo.com/sc/us/2fa/json?api_key=73c1f658&api_secret=epH63U0Ad0WRSCv8&to=".$recipients."&pin=".$access_code;
		
		curl_setopt_array($curl, array(
		  CURLOPT_URL => $url,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "GET",
		));

		$response = curl_exec($curl);

		curl_close($curl);
		//echo $response;
		return ['status' => 1, 'message' => "Send sms successfully"];
    }
}

if (!function_exists('twilio_lookup')) {
    function twilio_lookup($phone_number)
    {
        try{
            $account_sid = config('services.twilio.twilio_sid');
            $auth_token = config("services.twilio.twilo_auth_token");

            $twilio = new Client($account_sid, $auth_token);

            // $phone_number = $twilio->lookups->v1->phoneNumbers("+15108675310")
            //                                     ->fetch(["type" => ["carrier"]]);

            $phone_number = $twilio->lookups->v1->phoneNumbers($phone_number)
            ->fetch(["type" => ["carrier"]]);

            return ['status' => 1, 'data' => $phone_number];
        } catch (\Exception $e) {
            return ['status' => 0, 'message' => $e->getMessage()];
        }
    }
}


if(!function_exists('mailjet_send_mail')){
    function mailjet_send_mail($data){
        try{
            $curl = curl_init();
            $authentication_token = config('services.mailjet.mailjet_token');

            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.mailjet.com/v3.1/send",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS =>'{
                    "Messages":[
                    {
                        "From":{
                            "Email":"notifications@unstuq.com",
                            "Name":"UnstuQ"
                        },
                        "To":[
                            {
                                "Email":"'.$data["SendTo"].'",
                                "Name":"'.$data["FullName"].'"
                            }
                        ],
                        "TemplateID":'.$data['TemplateID'].',
                        "TemplateLanguage":true,
                        "Subject":"'.$data['MailSubject'].'",
                        "Variables": {
                            "user_name":"'.$data['UserName'].'",
                            "access_code":"'.$data['Code'].'"
                        }
                    }
                    ]
                }',
                CURLOPT_HTTPHEADER => array(
                    "Content-Type: application/json",
                    "Authorization: Basic ".$authentication_token
                ),
            ));

            $response = curl_exec($curl);
            curl_close($curl);

            return ['status' => 1, 'message' => "Send mail successfully"];
        } catch (\Exception $e) {
            return ['status' => 0, 'message' => $e->getMessage()];
        }
    }
}

if(!function_exists('get_random_strings')){
    function get_random_strings($length_of_string)
    {
        $str_result = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        return substr(str_shuffle($str_result),
                        0, $length_of_string);
    }
}


if (! function_exists('resizeAndUploadFile')) {
    function resizeAndUploadFile($file, $filePath = '', $fileName = '') {
        try{
            if(isset($file)){
                $sourceProperties = getimagesize($file->getRealPath());
                $imageType = $sourceProperties[2];
                $ext = $file->getClientOriginalExtension();
                $maxWidth = 200;
                $maxHeight = 0;
                $quality = 100;
                $sourceImage = $file->getRealPath();
                // $app_path = base_path()."/storage/app/public/";
                $app_path = base_path()."/public/uploads/";
                //$app_path = '/var/www/html/project/storage/app/public/';
                $targetImage = $app_path.$filePath."/". $fileName.".". $ext;
                if(resizeImage($sourceImage, $targetImage, $maxWidth, $maxHeight, $quality ,$imageType)){
                    return 'public/'.$filePath."/". $fileName.".". $ext;
                }else{
                    throw new \Exception("Error while processing request", 1);
                }
            }
        } catch (\Exception $e) {
            return '';
        }
    }
}


if (! function_exists('resizeImage')) {
    function resizeImage($sourceImage, $targetImage, $maxWidth, $maxHeight, $quality = 80,$type){
        ini_set('memory_limit', '0');
        try{
            // Obtain image from given source file.
            if($type == IMAGETYPE_PNG){
                $image = @imagecreatefrompng($sourceImage);
            } else  if($type == IMAGETYPE_JPEG){
                $image = @imagecreatefromjpeg($sourceImage);
            } else{
                return false;
            }

            // if (!$image = @imagecreatefromjpeg($sourceImage)){
            //     return false;
            // }

            // Get dimensions of source image.
            list($origWidth, $origHeight) = getimagesize($sourceImage);

            if ($maxWidth == 0){
                $maxWidth  = $origWidth;
            }

            if ($maxHeight == 0){
                $maxHeight = $origHeight;
            }

            // Calculate ratio of desired maximum sizes and original sizes.
            $widthRatio = $maxWidth / $origWidth;
            $heightRatio = $maxHeight / $origHeight;

            // Ratio used for calculating new image dimensions.
            $ratio = min($widthRatio, $heightRatio);

            // Calculate new image dimensions.
            $newWidth  = (int)$origWidth  * $ratio;
            $newHeight = (int)$origHeight * $ratio;

            // Create final image with new dimensions.
            $newImage = imagecreatetruecolor($newWidth, $newHeight);

            imagecopyresampled($newImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);

            $parts = explode("/",$targetImage);
            unset($parts[count($parts)-1]);
            $folder_path = implode("/",$parts);

            if(!file_exists($folder_path)){
                mkdir($folder_path);
            }

            if($type == IMAGETYPE_PNG){
                $q = 9/100;
                $quality*=$q;
                imagepng($newImage, $targetImage, $quality);
            } else  if($type == IMAGETYPE_JPEG){
                imagejpeg($newImage, $targetImage, $quality);
            } else{
                return false;
            }
            // Free up the memory.
            imagedestroy($image);
            imagedestroy($newImage);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}


if (! function_exists('uploadFile')) {
    function uploadFile($file, $filePath = '', $fileName = '') {
        try{
        //File Upload if not empty
        if(isset($file))
        {
            //check file type
            $type = $file->getMimeType();
            $images = [
                'image/jpeg',
                'image/png'
            ];
            // $filePath = "/public/".$filePath;
            if(in_array($type,$images)){
                if($file->getSize() > 1000000){ // 1mb
                    $fileResult = resizeAndUploadFile($file, $filePath, $fileName);
                    if(empty($fileResult)){
                        $fileResult = $file->storeAs($filePath, $fileName.'.'.$file->getClientOriginalExtension(),"public_uploads");
                    }
                } else{
                    $fileResult = $file->storeAs($filePath, $fileName.'.'.$file->getClientOriginalExtension(),"public_uploads");
                }
            } else{
                $fileResult = $file->storeAs($filePath, $fileName.'.'.$file->getClientOriginalExtension());
            }
        //    dd($fileResult);
            if($fileResult) {
                // return str_replace("public/","",$fileResult);
                return $fileResult;
            } else {
                return false;
            }

            //$file_result = $request->file('image')->store('public/company-logo');
        }
        } catch (\Exception $e) {
            return false;
        }
    }

}

if(!function_exists('getLatLong')){
    function getLatLong($address){
        if(!empty($address)){
            //Formatted address
            $formattedAddr = str_replace(' ','+',$address);
            //Send request and receive json data by address
            $geocodeFromAddr = file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?address='.$formattedAddr.'&key=AIzaSyBeSQhL967LUo8OSgXuMQsdn1YOoDJvjrA');
            $output = json_decode($geocodeFromAddr);

            //Get latitude and longitute from json data
            $data['latitude']  = $output->results[0]->geometry->location->lat;
            $data['longitude'] = $output->results[0]->geometry->location->lng;
            //Return latitude and longitude of the given address
            if(!empty($data)){
                return $data;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
}