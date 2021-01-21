<?php

return [

    'trending_count' => 30,

    'min_favorite_count' => 10,

    'max_search_results' => 20,

    'compulsory_likes' => 5,

    'notify_x_hours_before_deadline' => 1,

    'no_of_photos' => 5, // max allowed 50

    'search_event_types_reverse' => [
        'restaurants' => 1,
        'attractions' => 2,
        'hotels' => 3,
        'movie' => 4,
        'tv' => 5,
        'customSearch'=>6,
        'events'=>7,
        'shopping'=>8,
        'date_idea'=>9
    ],

    'search_event_types' => [
       1 => 'restaurants',
       2 => 'attractions',
       3 => 'hotels' ,
       4 => 'movie',
       5 => 'tv',
       6=> 'customSearch',
       7=> 'events',
       8=> 'shopping',
       9=> 'date_idea'
    ],

    'result_data_types'=>[
       1 => 'restaurants',
       2 => 'activities',
       3 => 'hotels' ,
       4 => 'movies',
       5 => 'tv',
       6=> 'customSearch',
       7=> 'events',
       8=> 'shopping',
       9=> 'date_idea'
    ],

    'search_event_name' => [
        1 => 'dine out with :user at :location_name',
        2 => 'go with :user at :location_name',
        3 => 'hotel stay with :user at :location_name' ,
        4 => 'watching :movie_name with :user',
        5 => 'watching :tv_show_name with :user'
     ],

    'search_types_reverse' => [
        'solo' => 1,
        'group' => 2,
    ],

    'search_types' => [
        1 => "solo",
        2 => "group",
    ],

    //note please do not change text with colon prefix like :name as they are variables dynamicaly auto filled from code

    'register_message' => 'Hi :name, welcome to UnstuQ. We are glad to have you as part of our team. Your access code is: :access_code',

    'login_access_code_message' => "Welcome back to UnstuQ. Your access code is: :access_code",

    'pending_search_message' => "No Match Yet. Still waiting for votes from other crew members.",

    'solo_no_match_message' => "Since you did not like any of the options we gave you, please edit your search parameters and try again",

    'search_full_match_message' => "Hooray! We got a match. You're going to :location_name located at :location_address",

    'search_full_match_message_other' => "Hooray! We got a match. You matched on :item_name",

    'search_full_match_message_movie' => "Hooray! We got a match. You're watching :movie_name",

    'search_majority_match_message' => "We got a match based on majority votes. You're going to :location_name located at :location_address",

    'search_no_match_message' => "Unfortunately we did not get a match. We have selected location :location_name located at :location_address for you.",

    'added_to_group_notification_message' => "Hi :invited_user_name, :user_invited_by just added you to their crew on UnstuQ. Come join the excitement and see what the buzz is all about. Download the app here. https://unstuq.com",

    'added_to_group_notification_pending_user_message' => "Hi :invited_user_name, :user_invited_by just added you to their crew on UnstuQ. Come join the excitement and see what the buzz is all about. Download the app here. https://unstuq.com",

    'search_invite_message' => "Hi :invited_user_name, :user_invited_by just invited you to hangout. Help them pick where to go by downloading the UnstuQ app here. https://unstuq.com",
	
    'search_invite_message_push' => "Hi :invited_user_name, :user_invited_by just invited you to hangout. Help pick where to go in the UnstuQ app.",


    'pending_search_notification_message' => 'Hi :name, Please submit your likes for the invitation from :user_invited_by.The deadline is in :x_time.',

    'review_message' => "Did you enjoy your experience yesterday at :location_name ? Click here to submit a 1 minute review of your experience.",

    'group_add_text_message' => "Hi :invited_user_name, :user_invited_by just added you to their crew (:group_name) on UnstuQ using your number :user_phone_no. Download the UnstuQ app here: https://unstuq.com",

    'group_add_notification_message' => "Hi :invited_user_name, :user_invited_by just added you to their crew (:group_name) on UnstuQ.",

    'experience_add_notification_message' => "Hi :invited_user_name, :user_invited_by just shared an experience with you (:experience_title) on UnstuQ.",

    'notify_pending_message' => "Hi :invited_user_name, :user_invited_by just added you to their crew with your number ( :user_phone_no ) on UnstuQ.",

    'notify_pending_text_message' => "Hi :invited_user_name, :user_invited_by just added you to their crew with your number ( :user_phone_no ) on UnstuQ. Come join the excitement and see what the buzz is all about. Download the app here. https://unstuq.com",
    'shared_experience_message'=>'Hi :invited_user_name, :user_invited_by just shared an experience with you on UnstuQ.',

];