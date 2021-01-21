<?php

namespace App\Http\Controllers\Api;

use App\Repositories\Movies\MovieRepository;
use App\Repositories\Movies\MovieRepositoryInterface;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\User\UserRepositoryInterface;
use App\Repositories\UserGroupMember\UserGroupMemberRepositoryInterface;
use App\Repositories\Genre\GenreRepositoryInterface;
use App\Repositories\StreamingProvider\StreamingProviderRepositoryInterface;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Validator;
use App\Repositories\SearchTransaction\SearchTransactionRepositoryInterface;
use App\Repositories\SearchFilterOption\SearchFilterOptionRepositoryInterface;
use App\Repositories\SearchTransactionUser\SearchTransactionUserRepositoryInterface;
use App\Repositories\Business\BusinessRepositoryInterface;
use App\Repositories\MatchMaker\MatchMakerRepositoryInterface;
use App\Repositories\UserFavorite\UserFavoriteRepositoryInterface;
use App\Repositories\SearchTransactionGroup\SearchTransactionGroupRepositoryInterface;
use App\Repositories\UserDevice\UserDeviceRepositoryInterface;
use App\Config;

class SearchController extends Controller
{
    private $userRepository;
    private $userGroupMemberRepository;
    private $genreRepository;
    private $streamingProviderRepository;
    private $searchTransactionRepository;
    private $searchFilterOptionRepository;
    private $searchTransactionUserRepository;
    private $businessRepository;
    private $movieRepository;
    private $matchMakerRepository;
    private $userFavoriteRepository;
    private $searchTransactionGroupRepository;
    private $userDeviceRepository;


    public function __construct(UserRepositoryInterface $userRepository,
        UserGroupMemberRepositoryInterface $userGroupMemberRepository,
                                GenreRepositoryInterface $genreRepository,
                                StreamingProviderRepositoryInterface $streamingProviderRepository,
                                SearchFilterOptionRepositoryInterface $searchFilterOptionRepository,
                                SearchTransactionUserRepositoryInterface $searchTransactionUserRepository,
                                BusinessRepositoryInterface $businessRepository,
                                MovieRepositoryInterface $movieRepository,
                                SearchTransactionRepositoryInterface $searchTransactionRepository,
                                MatchMakerRepositoryInterface $matchMakerRepository,
                                UserFavoriteRepositoryInterface $userFavoriteRepository,
                                searchTransactionGroupRepositoryInterface $searchTransactionGroupRepository,
                                UserDeviceRepositoryInterface $userDeviceRepository){

        $this->userRepository = $userRepository;
        $this->userGroupMemberRepository = $userGroupMemberRepository;
        $this->genreRepository = $genreRepository;
        $this->streamingProviderRepository = $streamingProviderRepository;
        $this->searchTransactionRepository = $searchTransactionRepository;
        $this->searchFilterOptionRepository = $searchFilterOptionRepository;
        $this->searchTransactionUserRepository = $searchTransactionUserRepository;
        $this->businessRepository = $businessRepository;
        $this->movieRepository = $movieRepository;
        $this->matchMakerRepository = $matchMakerRepository;
        $this->userFavoriteRepository = $userFavoriteRepository;
        $this->searchTransactionGroupRepository = $searchTransactionGroupRepository;
        $this->userDeviceRepository = $userDeviceRepository;

        $this->middleware('jwt.auth');
    }

    /**
     * @api {post} /search/save_solo_match  Solo save matched
     * @apiName Solo save matched
     * @apiGroup Search
     *
     * @apiHeader {String} Authorization='bearer bd970a05-0ec1-4412-8b28-657962f0f778'
     * @apiParam {Numeric} search_id unique search identifier
     * @apiParam {String} matched_location_id matched_location_id for search
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *  {
     *     "status": "1",
     *     "data": ''
     *  }
     *
     */
    public function save_solo_match(Request $request){
        $response = ['status' => false, 'message' => 'Something Went wrong while processing'];
        $validator = Validator::make($request->all(), [
            'search_id' => 'required|numeric',
            'matched_location_id' => 'required',
        ]);

        if ($validator->fails()) {
            $response =  get_parsed_validation_error_response($validator);
            return response()->json($response, 400);
        }else{
            $data = $request->only('matched_location_id','search_id');
            $search = $this->searchTransactionRepository->findOneFromArray([
                'id' => $data['search_id'],
                'status' => 0
            ]);

            if($search){
                //check if entity id is part of user liked
                $check = $this->matchMakerRepository->findOneFromArray([
                    'search_transaction_id' => $data['search_id'],
                    'entity_id' => $data['matched_location_id'],
                ]);

                if($check){
                    //update macthed entity
                    $this->searchTransactionRepository->update([
                        'matched_entity_id' => $check['entity_id'],
                        'status' => 1
                    ],$search->id);
                    $response = [
                        'status' => true,
                        'message' => 'successfully saved matched'
                    ];
                }else{
                    $response['message'] = "Invalid Location Id given.";
                    return response()->json($response,'400');
                }

            }else{
                $response['message'] = "Either Search not found or already updated";
                return response()->json($response,'404');
            }

        }
        return response()->json($response,'200');
    }


    /**
     * @api {get} /location/search_matched_pending  Get pending and matched
     * @apiName Get pending and matched
     * @apiGroup Search
     *
     * @apiHeader {String} Authorization='bearer bd970a05-0ec1-4412-8b28-657962f0f778'
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *  {
     *     "status": "1",
     *     "data": {
     *                     'pending' => [],
     *                     'matched' => []
     *      }
     *  }
     *
     */
    public function search_matched_pending(Request $request){

        $response = ['status' => false, 'message' => 'Something Went wrong while processing'];
        // try{

            $user = JWTAuth::toUser($request->token);
            $search_event_types = config('constant.search_event_types');
            $search_types = config('constant.search_types');
            //get only last 7 days match results
            $x_days = ( 7 * 24 * 60 * 60 );
            $x_days_before = strtotime(gmdate("Y-m-d H:i:s")) -  $x_days;
            $check = gmdate("Y-m-d H:i:s",$x_days_before);
            $today =date("Y-m-d H:i:s");


            $groups= $this->userGroupMemberRepository->findMultipleFromArray([
                ['user_id', '=',$user->id],
            ]);

            $grps_arr = [];
            foreach($groups as $grp){
                $grps_arr[] =$grp->user_group_id;
            }

          $pending_searches = $this->searchTransactionRepository->get_pending_data($grps_arr,$status = 'pending',$today,$user->id);

			//ALMA ADDED USER ID TO THE SEARCH CONDITIONS - 07/21/2020


            /* $pending_searches = $this->searchTransactionRepository->findMultipleFromArray([
               // ['user_id', '=',$user->id],
                ['deadline', '>=',$today],
                ['status', '=', '0'],
            ]);*/
            $pending_data = [];

            if($pending_searches){
                if(count($pending_searches) == 0){
                    $pending_data = [];
                }else{
                    foreach($pending_searches as $search){
						$details = $search->results;
                        $dKey = json_decode($details,true)[config('constant.result_data_types')[$search->search_type]];
                        $voteResults = [];
                        // for($i = 0; $i < count($dKey); $i++)
                        // {
                        //     // $votes = getVotes($search->id,$i);
                        //     $votes = $this->searchTransactionRepository->getVotes($search->id,$i);
                        //     // $votes = 2;
                        //     $voteResults[$i]["index"] = $i;
                        //     $voteResults[$i]["votes"] = $votes;
                        // }

						//get any user who are not avialable or skipped the event
                        $skippedUsers = $this->searchTransactionUserRepository->findMultipleFromArray([
                            'search_transaction_id' => $search->id
                        ])->toArray();

                        //get users attending to event
                        $unique_members = get_search_users_attending_event($search, $skippedUsers);
                        $group_array = [];
                        $groupUsers = $this->searchTransactionGroupRepository->getgroupdetails($search->id);
                        if($groupUsers) {
                            foreach ($groupUsers as $grp) {
                                unset($grp->user_group_id);
                                unset($grp->search_transaction_id);
                                foreach ($grp->user_group as $user_grp) {
                                    $group_array = [];
                                    $group_array['id'] = $user_grp['id'];
                                    $group_array['name'] = $user_grp['group_name'];

                                }
                            }
                        }
                            $votes = $this->searchTransactionRepository->getVotes($search->id);
                        $pending_data[] = [
                            'search_id' => $search->id,
                            'type' => $search_event_types[$search->search_type],
                            'search_user_type' => $search_types[$search->search_user_type],
                            'search_title' => $search->search_title,//pending to do like solo dine out,dine out with family group
                            'meet_time' => $search->meet_time,
                            'location_name' => $search->location_name == null ? "N/A" : $search->location_name,
                            'group_id' => isset($group_array['id']) ? $group_array['id'] : 0,
                            'group_name' => isset($group_array['name']) ? $group_array['name'] : '',
                            'is_response_submitted' => $this->matchMakerRepository->get_like_dislike_status($user->id,$search->id),
                            'users' => $unique_members,
                            'search_results' => json_decode($search->results,true),
                            'votes' => $votes
                        ];
                    }
                }
            }

            $matched_searches = $this->searchTransactionRepository->get_pending_data($grps_arr,$status = 'match',$check,$user->id);
            //check search_id exists
			//ALMA ADDED USER ID TO THE SEARCH CONDITIONS - 07/21/2020
           /* $matched_searches = $this->searchTransactionRepository->findMultipleFromArray([
                ['status', '=', '1'],
                ['created_at', '>=',$check ],
				['user_id', '=',$user->id],
            ]);*/

            $matched_data = [];
            if($matched_searches){
                if(count($matched_searches) == 0){
                    $matched_data = [];
                }else{

                    foreach($matched_searches as $search)
					{
						$searchType = "";
						if($search->network == '' || $search->network == null)
						{
							$searchType = "location";
							$details = json_decode($search->results);  //already parsed data saved in database
							$matched_details = '';
                            // dd($details);
                            $dataKey = config('constant.result_data_types')[$search->search_type];
                            $details = $details->$dataKey;
                            $matched_details=json_decode(json_encode($details[$search->matched_entity_id]),true);
                                // dd($matched_details);
       //                      foreach ($dkey as $key => $value) {
                                
       //                      }
       //                      // [$details]
							// foreach($details as $key => $detail){
							// 	if(isset($detail->location_id)){
							// 		if($detail->location_id == $search->matched_entity_id){
							// 			$matched_details = json_decode(json_encode($detail), true);
							// 		}
							// 	}
							// }
						}
						else
						{
							$searchType = "movie";
							// $details = json_decode($search->results);  //already parsed data saved in database
							// $matched_details = '';
							// foreach($details->results as $key => $object){
							// 	if(isset($object->id)){
							// 		if($object->id == $search->matched_entity_id){
							// 			$matched_details = json_decode(json_encode($object), true);
							// 		}
							// 	}
							// }
                            $details = json_decode($search->results);  //already parsed data saved in database
                            $matched_details = '';
                            // dd($details);
                            $dataKey = config('constant.result_data_types')[$search->search_type];
                            $details = $details->$dataKey;
                            $matched_details=json_decode(json_encode($details[$search->matched_entity_id]),true);
						}

                        //var_dump($matched_details->location_id);exit;
                        //get any user who are not avialable or skipped the event
                        $skippedUsers = $this->searchTransactionUserRepository->findMultipleFromArray([
                            'search_transaction_id' => $search->id
                        ])->toArray();

                        $group_array = [];
                        $groupUsers = $this->searchTransactionGroupRepository->getgroupdetails($search->id);
                        if($groupUsers) {
                            foreach ($groupUsers as $grp) {
                                unset($grp->user_group_id);
                                unset($grp->search_transaction_id);
                                foreach ($grp->user_group as $user_grp) {
                                    $group_array = [];
                                    $group_array['id'] = $user_grp['id'];
                                    $group_array['name'] = $user_grp['group_name'];

                                }
                            }
                        }

                        //get users attending to event
                        $unique_members = get_search_users_attending_event($search, $skippedUsers);
                        // dd($matched_details);
						if(count($matched_details)>0)
						{
                            if($searchType == "location")
							{
								$locationName = "";
								if(isset($matched_details["title"]))
								{
									$locationName = $matched_details["title"];
								}
                                else{
                                    $locationName = $matched_details["name"];
                                }
								//reviews
                        		// $reviews = $matched_details["num_reviews"]*1;
								$entity = ''.$search->matched_entity_id.'';
								$matched_data[] = [
									'search_id' => $search->id,
									'type' => $search_event_types[$search->search_type],
									'search_user_type' => $search_types[$search->search_user_type],
									'search_title' => $search->search_title,
									'meet_time' => $search->meet_time,
									'location_name' => $search->location_name,
									'matched_location' => $locationName,
									'matched_entity_id' => $entity,
									'group_id' => isset($group_array['id']) ? $group_array['id'] : 0,
									'group_name' => isset($group_array['name']) ? $group_array['name'] : '',
									'is_response_submitted' => $this->matchMakerRepository->get_like_dislike_status($user->id,$search->id),
									'users' => $unique_members,
									'matched_entity_details' => $matched_details,
								];
							}
							else
							{
								$movieName = "";
								if(isset($matched_details["title"]))
								{
									$movieName = $matched_details["title"];
								}
								$entity = ''.$search->matched_entity_id.'';
								$matched_data[] = [
									'search_id' => $search->id,
									'type' => $search_event_types[$search->search_type],
									'search_user_type' => $search_types[$search->search_user_type],
									'search_title' => $search->search_title,
									'meet_time' => $search->meet_time,
									'location_name' => "N/A",
									'matched_location' => $movieName,
									'matched_entity_id' => $entity,
									'group_id' => isset($group_array['id']) ? $group_array['id'] : 0,
									'group_name' => isset($group_array['name']) ? $group_array['name'] : '',
									'is_response_submitted' => $this->matchMakerRepository->get_like_dislike_status($user->id,$search->id),
									'users' => $unique_members,
									'matched_entity_details' => $matched_details,
								];
							}
                        }
                    }

                }
            }

             usort($pending_data, function($a, $b) {
                return $b['search_id'] <=> $a['search_id'];
            });

            usort($matched_data, function($a, $b) {
                return $b['search_id'] <=> $a['search_id'];
            });

            $response = [
                'status' => true,
                'data' => [
                    'pending' => $pending_data,
                    'matched' => $matched_data,
                ]
            ];
        // } catch (\Exception $e) {
        //     $response = error_reponse_handler($e);
        //     return response()->json($response['response'],$response['status_code']);
        // }
        return response()->json($response,'200');
    }

    /**
     * @api {get} /location/search_filters  Get all search filters
     * @apiName Get all search filters
     * @apiGroup Search
     *
     * @apiHeader {String} Authorization='bearer bd970a05-0ec1-4412-8b28-657962f0f778'
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *  {
     *     "status": "1",
     *     "data": ''
     *  }
     *
     */
    public function search_filters(Request $request){

        $response = ['status' => false, 'message' => 'Something Went wrong while processing'];
        try{
            $user = JWTAuth::toUser($request->token);
            if($user){
                //restaurants filters options
                $resturant_filters = $this->searchFilterOptionRepository->findMultipleFromArray([
                    'section_id' => 'combined_food',
                    'active' => '1'
                ]);

                //attraction filters options
                $attraction_filters = $this->searchFilterOptionRepository->findMultipleFromArray([
                    'section_id' => 'subtype',
                    'active' => '1'
                ]);

                //movie tv show filter options
                $networks =  $this->streamingProviderRepository->findMultipleFromArray([
                    'active' => 1
                ]);

                $movie_genres =  $this->genreRepository->findMultipleFromArray([
                    'type' => "movie"
                ]);

                $tv_genres =  $this->genreRepository->findMultipleFromArray([
                    'type' => "tv"
                ]);

                $response = [
                    'status' => true,
                    'data' => [
                        'attraction_type' => $attraction_filters,
                        'entertainment' => [
                            'category' => [
                                'movie' => 'Movies',
                                'tv' => 'Tv Shows'
                            ],
                            'genres' => [
                                'movie' =>  $movie_genres ,
                                'tv' =>  $tv_genres
                            ],
                            'networks' => $networks
                        ],
                        'combined_food' => $resturant_filters,
                    ]
                ];
            }
        } catch (\Exception $e) {
            $response = error_reponse_handler($e);
            return response()->json($response['response'],$response['status_code']);
        }
        return response()->json($response,200);

    }

    /**
     * @api {post} /location/like_dislike  Like dislike location
     * @apiName Like dislike location
     * @apiGroup Search
     *
     * @apiHeader {String} Authorization='bearer bd970a05-0ec1-4412-8b28-657962f0f778'
     * @apiParam {String} search_id  search_id
     * @apiParam {String} location_ids  location_id json string : [    {       "location_id":13388091,       "like_dislike":1    } ]
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *  {
     *     "status": "1",
     *     "data": ''
     *  }
     *
     */
    public function like_dislike_search(Request $request){
        // dd($request->location_ids);
        $response = ['status' => false, 'message' => 'Something Went wrong while processing'];
        $validator = Validator::make($request->all(), [
            'search_id' => 'required|numeric',
            'location_ids' => 'required',
        ]);

        if ($validator->fails()) {
            $response =  get_parsed_validation_error_response($validator);
            return response()->json($response, 400);
        }else{
            // try{
                $user = JWTAuth::toUser($request->token);
                if($user){

                    $data = $request->only('search_id','location_ids');

                    //check search_id exists and if not already submitted
                    $search = $this->searchTransactionRepository->findOneFromArray([
                        'id' => $data['search_id'],
                        'status' => 0
                    ]);
                    if($search){

                        //get search type id
                        $search_type_id = $search->search_type;
                        $search_network  =  $search->network ;
                        //check if user has already submitted results
                        $submitted_results_found = $this->matchMakerRepository->findOneFromArray([
                            'search_transaction_id' => $data['search_id'],
                            'user_id' =>  $user->id
                        ]);

                       // if(isset($submitted_results_found) ){
                        if(isset($submitted_results_found)){
                            $response['message'] = "Already submitted response";
                            return response()->json($response,'404');

                        }else{
                            //dd($data['location_ids']);
                            $location_ids = '';
                            if(is_string($data['location_ids'])){
                                if(isJson($data['location_ids'])){
                                    $location_ids = json_decode($data['location_ids'],true);

                                }else{
                                    $response['message'] = "Invalid json provided";
                                    return response()->json($response,'400');
                                }
                            }else{
                                $location_ids = $data['location_ids'];
                            }
                            // dd($search->search_type);
                            // dd(config("constant.result_data_types"));
                            $key_type= config("constant.result_data_types")[$search->search_type];
                            $search_entities = json_decode($search->results);
                            // dd($search_entities,$key_type);
                            $search_entities = $search_entities->$key_type;
                            // 
                            // dd($location_ids);
                            
                            $idCats = array_column($location_ids, 'location_id');
                            // dd($search_entities);
                            $search_entities_data = [];
                            for($i = 0; $i < count($search_entities); $i++)
                            {
                                if(in_array($i, $idCats))
                                {
                                    $search_entities_data[$i][] = $search_entities[$i];
                                }
                            }
                            // dd($search_entities_data);
                            // foreach($search_entities as $entity){
                            //     // strpos($location_ids[0]['location_id'], needle)
                            //     $temp_key = explode('_', $location_ids[0]['location_id']);
                            //     // echo $temp_key[0]."_".$temp_key[1];die();
                            //     //dd($entity->location_id, $location_ids);
                            //     if(in_array($search_type_id,[4,5])){
                            //         if (find_object_key_value_array($location_ids, 'location_id', $entity->id)) {
                            //             $search_entities_data[$entity->id][] = $entity;
                            //         }

                            //     }else {
                            //         if (find_object_key_value_array($location_ids, 'location_id', $entity->location_id)) {
                            //             $search_entities_data[$entity->location_id][] = $entity;
                            //         }
                            //     }
                            // }
                            $data_liked_locations = [];
                            // dd($search_entities_data);
                            foreach($location_ids as $location_id){

                                //$entities = json_decode($search->results)->results;
                                //$entities = json_decode($search->results);
                                //$entities = $search_entities->$key_type;

                                // if(in_array($search_type_id,[4,5])) {
                                //     if (find_object_key_value($entities, 'id', $location_id['location_id'])) {
                                //         $this->matchMakerRepository->create([
                                //             'user_id' => $user->id,
                                //             'search_transaction_id' => $data['search_id'],
                                //             'entity_id' => $location_id['location_id'],
                                //             'like_dislike' => $location_id['like_dislike'],
                                //             'created_at' => gmdate('Y-m-d H:i:s')
                                //         ]);

                                //         if ($location_id['like_dislike'] == 1) {

                                //             $location_details = $search_entities_data[$location_id['location_id']];
                                //             $data_liked_locations[] = $location_details;
                                //             //check if already added to favorite
                                //             $exists = $this->userFavoriteRepository->findOneFromArray([
                                //                 'entity_id' => $location_id['location_id'],
                                //                 'type' => $search_type_id,
                                //                 'user_id' => $user->id
                                //             ]);

                                //             if (!$exists) {
                                //                 // add to favorite
                                //                 $favortie = $this->userFavoriteRepository->create([
                                //                     'entity_id' => $location_id['location_id'],
                                //                     'type' => $search_type_id,
                                //                     'user_id' => $user->id
                                //                 ]);

                                //                     //check if exists
                                //                     $exists_movie = $this->movieRepository->findOneFromArray([
                                //                         'property_id' => $location_id['location_id'],
                                //                         'type' => $search_type_id
                                //                     ]);
                                //                     //dd($exists_movie,$location_details);
                                //                     if (!$exists_movie) {
                                //                         foreach($location_details as $loc){
                                //                             if($search_type_id == 4)
                                //                             {
                                //                                 $name = $loc->title;
                                //                             } else if ($search_type_id == 5) {
                                //                                 $name = $loc->name;
                                //                             }
                                //                             //save location/event details
                                //                             if($name){
                                //                                 $this->movieRepository->create([
                                //                                     'name' => $name,
                                //                                     'property_id' => $loc->id,
                                //                                     'type' => $search_type_id,
                                //                                     'vote_count' => $loc->vote_count,
                                //                                     'vote_average' => $loc->vote_average,
                                //                                     'overview' => $loc->overview,
                                //                                     'genre' => implode($loc->genre_ids),
                                //                                     'network' => $search_network,
                                //                                     'details' => json_encode($location_details),
                                //                                 ]);
                                //                             }
                                //                         }


                                //                     } else {
                                //                         $response['note'] = "already added to movies";
                                //                     }

                                //             } else {
                                //                 $response['note'] = "already added to favorite";
                                //             }
                                //         }
                                //     } else {
                                //         $response['note'] = "movie not part of search  found";
                                //     }
                                // } else {
                                //if(find_object_key_value($entities, 'location_id', $location_id['location_id'])){
                                    $this->matchMakerRepository->create([
                                        'user_id' => $user->id,
                                        'search_transaction_id' => $data['search_id'],
                                        'entity_id' => $location_id['location_id'],
                                        'like_dislike' =>  $location_id['like_dislike'],
                                        'created_at' => gmdate('Y-m-d H:i:s')
                                    ]);

                                    //if( $location_id['like_dislike'] == 1){
                                        // dd($search_entities_data);
                                        $location_details = $search_entities_data[$location_id['location_id']];
                                        $data_liked_locations[] = $location_details;
                                        //check if already added to favorite
                                        // $exists = $this->userFavoriteRepository->findOneFromArray([
                                        //     'entity_id' => $location_id['location_id'],
                                        //     'type' => $search_type_id,
                                        //     'user_id' => $user->id
                                        // ]);

                                        // if(!$exists){
                                        //     // add to favorite
                                        //     $favortie = $this->userFavoriteRepository->create([
                                        //         'entity_id' => $location_id['location_id'],
                                        //         'type' => $search_type_id,
                                        //         'user_id' => $user->id
                                        //     ]);

                                        //     //depending on type check and save in buisness or movies table
                                        //     if(in_array($search_type_id,[4,5])){
                                        //         //for movie and tv - pending to do

                                        //     }else{
                                        //         //check if exists
                                        //         $exists = $this->businessRepository->findOneFromArray([
                                        //             'location_id' => $location_id['location_id'],
                                        //             'type' => $search_type_id
                                        //         ]);
                                        //         if(!$exists){
                                        //             //save location/event details
                                        //             foreach($location_details as $loc){
                                        //                 $this->businessRepository->create([
                                        //                     'name' => $loc->name ,
                                        //                     'location_id' => $loc->location_id ,
                                        //                     'longitude' => $loc->longitude ,
                                        //                     'latitude' => $loc->latitude,
                                        //                     'rating' => !empty($loc->rating) ? $loc->rating : 0,
                                        //                     'ranking' => $loc->ranking ,
                                        //                     'info' => json_encode($location_details),
                                        //                     'type' => $search_type_id
                                        //                 ]);
                                        //             }

                                        //         }else{
                                        //             $response['note'] = "already added to buisness";
                                        //         }
                                        //     }
                                        // }else{
                                        //     $response['note'] = "already added to favorite";
                                        // }
                                    //}
                                //}else{
                                //    $response['note'] = "location not part of search  found";
                                //}
                                // }
                            }

                            //check if all users submitted response for group search otherwise for solo return liked responses

                            //get any user who are not avialable or skipped the event
                            $skippedUsers = $this->searchTransactionUserRepository->findMultipleFromArray([
                                'search_transaction_id' => $search->id
                            ])->toArray();

                            $users_attending = get_search_users_attending_event($search, $skippedUsers);
                            //check if all attending users submitted
                            $count_submitted_users = $this->matchMakerRepository->checkUsersSubmitted([
                                'search_transaction_id' => $search->id
                            ])->toArray();
                            if( count($count_submitted_users) > 0){
                                if(count($users_attending) == count($count_submitted_users)){
                                    //run match making
                                    $max_liked_entity = $this->matchMakerRepository->get_liked_entity_count($search->id);

                                    //if no match found use first location liked by user search -- pending to do
                                    if(empty($max_liked_entity)){
                                         $first_liked_entity = $this->matchMakerRepository->get_first_liked_entity($search->id,$search->user_id);
                                         $max_liked_entity = $first_liked_entity;
                                    }

                                    //get reviews for matched entity
                                    // $location_reviews = '';
                                    // $host = env('SEARCH_API_HOST');
                                    // $key = env('SEARCH_API_KEY');
                                    // $search_type_name = config('constant.search_event_types')[$search->search_type];
                                    // $url = env('SEARCH_API_URL')."/".$search_type_name."/get-details";
                                    // $data['api_key'] =  env('API_KEY');
                                    // $data['location_id'] = $max_liked_entity['entity_id'];
                                    // $data['currency'] = 'USD';
                                    // $data['lang'] = "en-US";
                                    // $headers = [ 'Content-Type: application/json',
                                    //     'x-rapidapi-host: '.$host,
                                    //     'x-rapidapi-key: '.$key ];
                                    // $method = "GET";
                                    // $location_details = callAPI($method, $url, $data , $headers);
                                    // if($location_details){
                                    //     if(property_exists($location_details,'reviews')){
                                    //         $location_reviews = $location_details->reviews;
                                    //     }
                                    // }
                                    //update macthed entity
                                    $this->searchTransactionRepository->update([
                                        'matched_entity_id' => $max_liked_entity['entity_id'],
                                        'status' => 1
                                    ],$search->id);
                                    $match_entities_data = [];

                                    for($i = 0; $i < count($search_entities); $i++)
                                    {
                                        if ($max_liked_entity['entity_id'] == $i) {
                                                $match_entities_data[$max_liked_entity['entity_id']][] = $search_entities[$i];
                                            }
                                    }

                                    // foreach($search_entities as $entity){
                                    //     // if(in_array($search_type_id,[4,5])){
                                    //     //     if ($max_liked_entity['entity_id'] == $entity->id) {
                                    //     //         $match_entities_data[$entity->id][] = $entity;
                                    //     //     }

                                    //     // }else {
                                    //         if ($max_liked_entity['entity_id'] == $entity->location_id) {
                                    //             $match_entities_data[$entity->location_id][] = $entity;
                                    //         }
                                    //     // }
                                    // }
                                    $data_liked_locations = [];
                                    $data_liked_locations[] = $match_entities_data[$max_liked_entity['entity_id']];
                                    foreach($users_attending as $user){
                                        //notify all users with matched search results
                                        
                                        $user_device = $this->userDeviceRepository->findOneFromArray([
                                            'user_id' => $user['id']
                                        ]);

                                        /*if($user_device)
                                        {
                                            $type = 'match';
                                            if(in_array($search_type_id,[4,5])) {
                                                $msg_data = [
                                                    'movie_name' => isset($location_details->title) ? $location_details->title :''
                                                ];
                                                $message_name = 'search_full_match_message_movie';
                                                $msg = get_message_text($message_name, $msg_data);
                                                sendMessage($user_device["player_id"],$msg,$type);

                                            } 
                                            else if(in_array($search_type_id,[6])) {
                                                $msg_data = [
                                                    'item_name' => isset($data_liked_locations->name 
                                                ];
                                                $message_name = 'search_full_match_message_other';
                                                $msg = get_message_text($message_name, $msg_data);
                                                sendMessage($user_device["player_id"],$msg,$type);

                                            }
                                            else if(in_array($search_type_id,[8,9])) {
                                                $msg_data = [
                                                    'item_name' => isset($location_details->title) ? $location_details->title :'', 
                                                ];
                                                $message_name = 'search_full_match_message_other';
                                                $msg = get_message_text($message_name, $msg_data);
                                                sendMessage($user_device["player_id"],$msg,$type);

                                            }
                                            else{
                                                $msg_data = [
                                                    'location_name' => isset($location_details->title) ? $location_details->title :'',
                                                    'location_address' => isset($location_details->address) ? $location_details->address :''
                                                ];
                                                $message_name = 'search_full_match_message';
                                                $msg = get_message_text($message_name, $msg_data);
                                                //$max_liked_entity['entity_id'],
                                                //'search_id' => $search->id
                                                //REMEMBER TO SEND THE ENTITY INFO TO THE SEND MESSAGE FUNCTION
                                                sendMessage($user_device["player_id"],$msg,$type);
                                            }
                                        }*/
                                    }
                                }else{
                                    $data_liked_locations = [];
                                }
                            }
                            $final_entity_data = new \stdClass();
                            if(isset($data_liked_locations))
                            {
                                $liked_data = $data_liked_locations;
                                $final_entity_data = $liked_data;
                                // if(!empty($liked_data->location_id))unset($liked_data->location_id);
                                // $final_entity_data->name = isset($liked_data->name)? $liked_data->name : $liked_data->title;
                                // $final_entity_data->photo = isset($liked_data->photo)? $liked_data->photo : $liked_data->poster_path;
                                // $final_entity_data->address = isset($liked_data->address)? $liked_data->address :'';
                                // $final_entity_data->phone = isset($liked_data->phone)? $liked_data->phone :'';
                                // $final_entity_data->description = isset($liked_data->description)? $liked_data->description :$liked_data->overview;
                                // $final_entity_data->rating = isset($liked_data->rating)? (double)$liked_data->rating :(double)$liked_data->vote_average;
                                // $final_entity_data->ranking = isset($liked_data->ranking)? $liked_data->ranking :'';
                                // $final_entity_data->location_string = isset($liked_data->location_string)? $liked_data->location_string :'';
                                // $final_entity_data->latitude = isset($liked_data->latitude)? $liked_data->latitude :'';
                                // $final_entity_data->longitude = isset($liked_data->longitude)? $liked_data->longitude :'';
                                // $final_entity_data->price = isset($liked_data->price)? $liked_data->price :'';
                                // $final_entity_data->cuisine = isset($liked_data->cuisine)? $liked_data->cuisine :'';
                                // $final_entity_data->distance_string = isset($liked_data->distance_string)? $liked_data->distance_string :'';
                                // $final_entity_data->num_reviews = isset($liked_data->num_reviews)? $liked_data->num_reviews :'';
                                // $final_entity_data->popularity = isset($liked_data->popularity)? $liked_data->popularity :0.0;
                                // $final_entity_data->vote_count = isset($liked_data->vote_count)? $liked_data->vote_count :0;
                                // $final_entity_data->genre_ids = isset($liked_data->genre_ids)? $liked_data->genre_ids :[];
                                // $final_entity_data->release_date = isset($liked_data->release_date)? $liked_data->release_date :'';
                                //$final_entity_data->who_is_coming = isset($users_attending) ? $users_attending :[];
                            }
                            else{
                                $liked_data = new \stdClass();
                            }

                            $response = [
                                'status' => true,
                                'data' => ($final_entity_data == new \stdClass()) ? null : $final_entity_data,
                                'who_is_coming' => isset($users_attending) ? $users_attending :[],
                                'message' => 'Successfully Submitted'
                            ];
                        }

                    }else{
                        $response['message'] = "Search not found or already match found";
                        return response()->json($response,'404');
                    }
                }else{
                    $response['message'] = "User not found";
                    return response()->json($response,'404');
                }

            // } catch (\Exception $e) {
            //     $response = error_reponse_handler($e);
            //     return response()->json($response['response'],$response['status_code']);
            // }
        }
        return response()->json($response,'200');
    }

    /**
     * @api {get} /search/get  Get Search Results
     * @apiName Get Search Results
     * @apiGroup Search
     *
     * @apiHeader {String} Authorization='bearer bd970a05-0ec1-4412-8b28-657962f0f778'
     * @apiParam {String} search_id  search_id
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *  {
     *     "status": "1",
     *     "data": {
     *         "results": [
     *             {
     *                 "photo": "https://media-cdn.tripadvisor.com/media/photo-o/14/97/93/09/sweet-dream-a-potpourri.jpg",
     *                 "location_string": "Pattaya, Chonburi Province",
     *                 "num_reviews": "1162",
     *                 "name": "Casa Pascal Restaurant",
     *                 "location_id": "1130181",
     *                 "longitude": "100.87983",
     *                 "latitude": "12.928686",
     *                 "cuisine": "Thai",
     *                 "address": "485/4 Moo 10, Second Road Opposite Royal Garden Plaza, Pattaya 20260 Thailand",
     *                 "write_review": "https://www.tripadvisor.com/UserReview-g293919-d1130181-Casa_Pascal_Restaurant-Pattaya_Chonburi_Province.html",
     *                 "web_url": "https://www.tripadvisor.com/Restaurant_Review-g293919-d1130181-Reviews-Casa_Pascal_Restaurant-Pattaya_Chonburi_Province.html",
     *                 "description": "Happy Dining. Setting culinary standards in Pattaya since 2001. In this oasis of Pattaya we cook the food with freshest ingredients, most of them seasonal and from local and imported sources.",
     *                 "price": "$10 - $30",
     *                 "rating": "4.5",
     *                 "distance_string": "1.1 mi",
     *                 "ranking": "#24 of 1,300 Restaurants in Pattaya",
     *                 "website": "http://www.restaurant-in-pattaya.com/",
     *                 "phone": "+66 61 643 9969"
     *             }],
     *          "compulsory_likes": 5
     *      }
     *  }
     *
     */
    public function getCompulsoryLikes($count){
        // dd($this->result_count);
        if($count>=10){
            return 5;
        }
        if($count>=8 && $count<10){
            return 4;
        }
        if($count>=6 && $count<8){
            return 3;
        }
        if($count>=3 && $count<6){
            return 2;
        }
        if($count>=1 && $count<3){
            return 1;
        }
    }
    public function search_results(Request $request){
        $response = ['status' => false, 'message' => 'Something Went wrong while processing'];
        $validator = Validator::make($request->all(), [
            'search_id' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            $response =  get_parsed_validation_error_response($validator);
            return response()->json($response, 400);
        }else{
            // try{
                $data = $request->only('search_id');
                $user = JWTAuth::toUser($request->token);
                //check search_id exists
                $search = $this->searchTransactionRepository->find($data['search_id']);
                $search_types = config('constant.result_data_types')[$search->search_type];
                // dd($search_types);
                if($search){
                    // dd($request->all());
                    $parsed_data = get_parsed_search_response($search);
                    // dd($parsed_data->$search_types);
                    // $compulsory_likes = config('constant.compulsory_likes'); //will be 20
                    // $cnt_parsed_data= count($parsed_data->$search_types);
                    $compulsory_likes=$this->getCompulsoryLikes(count($parsed_data->$search_types));
                    // if($cnt_parsed_data >=9){
                    //     $compulsory_likes = 5;
                    // }else if($cnt_parsed_data ==8 || $cnt_parsed_data ==7){
                    //       $compulsory_likes = 4;
                    // }else if($cnt_parsed_data ==6 || $cnt_parsed_data ==5){
                    //       $compulsory_likes = 3;
                    // }else if($cnt_parsed_data <=4){
                    //         $compulsory_likes = 2;
                    // }
                    $response = [
                        'status' => true,
                        'data' => [
                            'compulsory_likes' => $compulsory_likes,
                            'result_count' => count($parsed_data->$search_types),
                            'search_id' => $search->id,
                            'search_title' => $search->search_title,
                            'deadline' => isset($search->deadline)? $search->deadline :'',
                            'has_initiated' => ($user->id==$search->user_id) ? true :false,
                            'search_results' => ['results' => $parsed_data]
                        ]
                    ];
                }else{
                    $response['message'] = "Search not found";
                    return response()->json($response,'404');
                }

            // } catch (\Exception $e) {

            //     $response = error_reponse_handler($e);
            //     return response()->json($response['response'],$response['status_code']);
            // }
        }
        return response()->json($response,'200');
    }


    /**
     * @api {get} /location/search  Search location
     * @apiName  Search location
     * @apiGroup Search
     *
     * @apiHeader {String} Authorization='bearer bd970a05-0ec1-4412-8b28-657962f0f778'
     * @apiParam {String} type restaurants,attractions,hotels
     * @apiParam {String} location_name location name
     * @apiParam {String} latitude current location latitude
     * @apiParam {String} longitude current location longitude
     * @apiParam {Number} group_id selected group id (pass zero for solo)
     * @apiParam {String} [deadline] required for group search
     * @apiParam {String} [prices_restaurants] comma separated values of price for restaurants
     * @apiParam {String} [combined_food] comma separated values of cuisine types for restaurants
     * @apiParam {String} [attraction_category] category filter for attractions
     * @apiParam {String} search_day user meet/event date 2012-05-23
     * @apiParam {String} search_time user meet/event time 22:00
     * @apiParam {String} search_title search title
     * @apiParam {String} [offset] for pagination default - 0
     * @apiParam {String} [lunit] unit of distance - mi or km ( default : mi )
     * @apiParam {Number} [distance] search raduis( default : 5 )
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *  {
     *     "status": "1",
     *     "data": {
     *          "search_id": 8,
     *          "deadline":"2020-07-28 02:00:00",
     *          "search_results": {
     *             "result_count": 5,
     *             "results": [
     *                 {
     *                     "photo": "https://media-cdn.tripadvisor.com/media/photo-o/14/97/93/09/sweet-dream-a-potpourri.jpg",
     *                     "location_string": "Pattaya, Chonburi Province",
     *                     "num_reviews": "1162",
     *                     "name": "Casa Pascal Restaurant",
     *                     "location_id": "1130181",
     *                     "longitude": "100.87983",
     *                     "latitude": "12.928686",
     *                     "cuisine": "Thai",
     *                     "address": "485/4 Moo 10, Second Road Opposite Royal Garden Plaza, Pattaya 20260 Thailand",
     *                     "write_review": "https://www.tripadvisor.com/UserReview-g293919-d1130181-Casa_Pascal_Restaurant-Pattaya_Chonburi_Province.html",
     *                     "web_url": "https://www.tripadvisor.com/Restaurant_Review-g293919-d1130181-Reviews-Casa_Pascal_Restaurant-Pattaya_Chonburi_Province.html",
     *                     "description": "Happy Dining. Setting culinary standards in Pattaya since 2001. In this oasis of Pattaya we cook the food with freshest ingredients, most of them seasonal and from local and imported sources.",
     *                     "price": "$10 - $30",
     *                     "rating": "4.5",
     *                     "distance_string": "1.1 mi",
     *                     "ranking": "#24 of 1,300 Restaurants in Pattaya",
     *                     "website": "http://www.restaurant-in-pattaya.com/",
     *                     "phone": "+66 61 643 9969"
     *                 }],
     *               "offset": 0
     *            },
     *            "compulsory_likes": 5
     *     }
     *  }
     *
     */
    public function location_search(Request $request){
        $response = ['status' => false, 'message' => 'Something Went wrong while processing'];
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:restaurants,attractions,hotels',
            'location_name' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'group_id' => 'required|numeric',
            'deadline' => 'required|date_format:Y-m-d H:i:s',
            'search_day' => 'required|date|date_format:Y-m-d',
            'search_title' => 'required',
            'search_time' => 'required|date_format:H:i',
            'lunit' => "in:mi,km",
            'distance' => 'numeric'
        ]);

        if ($validator->fails()) {
            $response =  get_parsed_validation_error_response($validator);
            return response()->json($response, 400);
        }else{
            try{
                $user = JWTAuth::toUser($request->token);
                if($user){

                    $data = $request->only('type', 'location_name', 'latitude', 'longitude', 'lunit', 'distance',
                        'search_day', 'search_time', 'offset', 'combined_food', 'attraction_category',
                        'prices_restaurants', 'group_id', 'deadline','search_title');


                    if($data['group_id'] != 0 && !isset($data['deadline'])){
                        $response['message'] =  'Deadline not provided';
                        return response()->json($response, 400);
                    }

                    $timestamp = strtotime($data['search_day']);
                    $search_day = date('w', $timestamp);

                    $search_time = explode(":",$data['search_time']);
                    $search_in_mins = ( $search_time[0] * 60) + $search_time[1];

                    if(!isset($data['lunit'])){
                        $data['lunit'] = 'mi';
                    }
                    if(!isset($data['distance'])){
                        $data['distance'] = '10';
                    }

                    if($data["type"] == "restaurants")
                    {
                        if($data["combined_food"] == "10597" || $data["combined_food"] == "10598" || $data["combined_food"] == "10599" || $data["combined_food"] == "10606")
                        {
                            $url = env('SEARCH_API_URL')."/".$data['type']."/list-in-boundary";
                        }
                        else
                        {
                            $url = env('SEARCH_API_URL')."/".$data['type']."/list-by-latlng";
                        }
                    }
                    
                    if($data['type'] == "attractions")
					{
						$url = env('SEARCH_API_URL')."/".$data['type']."/list-in-boundary";
					}

					$data['api_key'] =  env('API_KEY');

                    $data['limit'] = 30;
                    $data['currency'] = 'USD';
                    $data['lang'] = "en-US";

                    if($data['group_id'] == 0){
                        //solo search
                        $data['group_ids'] = [];
                    }else{
                        // $data['group_ids'] = explode(",",$data['group_id']);
                        $data['group_ids'] = [$data['group_id']];
                    }
                    $resp_data = search_and_parse( $url, $data , $search_day, $search_in_mins);
                    if(!empty($resp_data) && $resp_data["result_count"] > 0){
                         //save results in database for future reference
                        $search = $this->searchTransactionRepository->save_search_results($user, $resp_data, $data);
                        // $parsed_data = get_parsed_search_response($search);
                        $compulsory_likes = config('constant.compulsory_likes');
                        $response = [
                            'status' => true,
                            "message" => "Successful",
							'search_id' => $search->id,
                            /*'data' => [
                                'compulsory_likes' => $compulsory_likes,
                                'search_id' => $search->id,
                                'search_title' => $search->search_title,
                                'deadline' => isset($data['deadline'])? $data['deadline'] :'',
                                'search_results' => $resp_data

                            ]*/
                        ];
                    }else{
                         $response = [
                            'status' => false,
                            "message" => "Search not found"
                           /*'data' => [
                                'compulsory_likes' => $compulsory_likes,
                                'search_id' => $search->id,
                                'search_title' => $search->search_title,
                                'deadline' => isset($data['deadline'])? $data['deadline'] :'',
                                'search_results' => $resp_data

                            ]*/
                        ];
                    }

                }
            }catch(\Exception $e) {
                $response = error_reponse_handler($e);
                return response()->json($response['response'],$response['status_code']);
            }
        }

        return response()->json($response,200);
    }

    /**
     * @api {get} /location/get  Get location details
     * @apiName Get location details
     * @apiGroup Search
     *
     * @apiHeader {String} Authorization='bearer bd970a05-0ec1-4412-8b28-657962f0f778'
     * @apiParam {String} type  restaurants,attractions,hotels
     * @apiParam {String} location_id  unique location id
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *  {
     *     "status": "1",
     *     "data": {
     *       "location_detail": {
     *             "location_id": "6564765",
     *             "longitude": "100.87312",
     *             "latitude": "12.910579",
     *             "num_reviews": "30",
     *             "name": "Good Farmer Homemade & Hydroponics Farm",
     *             "description": "A fine cozy restaurant in a garden and hydroponics salad farm, the one and only in Pattaya. We serve variety of Breakfast, All Day Delicious International and Thai-tasted menu such as fresh hash-brown breakfast, salmon steak, tom-yum-kung, gang-kiew-wan and homemade bakery by our Big Sister. All delicious menus serve with fresh-cut salads from our own farm, Good Farmer Hydroponics. See You Soon^^",
     *             "price": "$50 - $280",
     *             "rating": "4.5",
     *             "ranking": "#205 of 1,445 places to eat in Pattaya",
     *             "phone": "+66 83 854 9266",
     *             "address": "308/13 Moo 12, Soi Thappaya 15, Thappaya Road, Pattaya 20150 Thailand",
     *             "reviews": "",
     *             "web_url": "https://www.tripadvisor.com/Restaurant_Review-g293919-d6564765-Reviews-Good_Farmer_Homemade_Hydroponics_Farm-Pattaya_Chonburi_Province.html",
     *             "cuisine": "Vegetarian Friendly",
     *             "menu_web_url": "",
     *             "email": "nin.kanokmanee@gmail.com",
     *             "website": "http://www.facebook.com/GoodFarmerHomemade/",
     *             "owners_top_reasons": "",
     *             "photo_count": "28",
     *             "photo": "https://media-cdn.tripadvisor.com/media/photo-w/06/85/17/8b/himmapan.jpg",
     *             "location_string": "Pattaya, Chonburi Province"
     *         },
     *       "photos": {
     *             "photos": [
     *                 {
     *                     "photo": "https://media-cdn.tripadvisor.com/media/photo-s/06/85/17/8f/himmapan.jpg",
     *                     "caption": "We serve good Thai and International foods, wow taste with fresh own-grown Good Farmer Salad.",
     *                     "helpful_votes": "1",
     *                     "published_date": "2014-09-15T03:12:23-0400"
     *                 }
     *             ]
     *        }
     *      }
     *  }
     *
     */
    public function get_location_detail(Request $request){
        $response = ['status' => false, 'message' => 'Something Went wrong while processing'];
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:restaurants,attractions,hotels',
            'location_id' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            $response =  get_parsed_validation_error_response($validator);
            return response()->json($response, 400);
        }else{
            try{
                $data = $request->only('location_id','type');

                $today =date('Y-m-d H:i:s');

                $url = env('SEARCH_API_URL')."/".$data['type']."/get-details";
                $data['api_key'] =  env('API_KEY');

                $data['currency'] = 'USD';
                $data['lang'] = "en-US";

                $host = env('SEARCH_API_HOST');
                $key = env('SEARCH_API_KEY');

                $headers = [ 'Content-Type: application/json',
                    'x-rapidapi-host: '.$host,
                    'x-rapidapi-key: '.$key ];
                $method = "GET";

                $results = callAPI($method, $url, $data , $headers);

				$location_details = get_parsed_location_detail($results);
				//die();
                //location tips
                //$url = env('SEARCH_API_URL')."/tips/list";

            /*    $data['currency'] = 'USD';
                $data['lang'] = "en-US";

                $host = env('SEARCH_API_HOST');
                $key = env('SEARCH_API_KEY');

                $headers = [ 'Content-Type: application/json',
                    'x-rapidapi-host: '.$host,
                    'x-rapidapi-key: '.$key ];
                $method = "GET";*/
                //$results = callAPI($method, $url, $data , $headers);
                //$location = parse_tips_data($results);

                //unset($location['paging']);
                //$location_details['location_tips']= $location['tips'];
                $search_transaction_details = $this->searchTransactionRepository->findMultipleFromArray([
                    ['matched_entity_id', '=', $location_details['location_id']],
                ]);

                foreach($search_transaction_details as $search){
                    $users_members = [];

                    if((date("Y-m-d", strtotime($search->meet_time))) > $today){
                        //get any user who are not available or skipped the event
                        $skippedUsers = $this->searchTransactionUserRepository->findMultipleFromArray([
                            'search_transaction_id' => $search->id
                        ])->toArray();
                        //get users attending to event
                        $users_members[] = get_search_users_attending_event($search, $skippedUsers);
                    }
                }
                $unique_members = array();
                if(!empty($users_members)){

                    foreach($users_members as $array) {
                        foreach($array as $k=>$v) {
                            $unique_members[$k] = $v;
                        }
                    }
                }


                //get atleast 10 from photos api add here to do ----
                // $photo_url = env('SEARCH_API_URL')."/photos/list";
                // $photos_count = config('constant.no_of_photos');
                // $photodata = [
                //     'currency' => 'USD',
                //     'lang' => "en-US",
                //     'location_id' => $data['location_id'],
                //     'limit' => $photos_count //max limit is 50
                // ];
                // $photo_results = callAPI($method, $photo_url, $photodata , $headers);
                // $parsed_photos = get_parsed_photos_response($photo_results);

                $response = [
                    'status' => true,
                    'data' => [
                        'location_detail' => $location_details,
                        'photos' => [],
                        'users_coming' => $unique_members
                    ]
                ];
            } catch (\Exception $e) {
                $response = error_reponse_handler($e);
                return response()->json($response['response'],$response['status_code']);
            }
        }

        return response()->json($response,200);
    }

    /**
     * @api {get} /location/get_tips  Get location tips
     * @apiName Get location tips
     * @apiGroup Search
     *
     * @apiHeader {String} Authorization='bearer bd970a05-0ec1-4412-8b28-657962f0f778'
     * @apiParam {String} type  restaurants,attractions,hotels
     * @apiParam {String} location_id  unique location id
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *  {
     *     "status": "1",
     *     "data": {
     *          "tips": [
     *             {
     *                 "username": "billyv419",
     *                 "type": "room_tip",
     *                 "text": "Room in the corner has the 360 sea viewi",
     *                 "rating": "4"
     *             }
     *           ]
     *      }
     *  }
     *
     */
    public function get_location_tips(Request $request){
        $response = ['status' => false, 'message' => 'Something Went wrong while processing'];
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:restaurants,attractions,hotels',
            'location_id' => 'required',
        ]);

        if ($validator->fails()) {
            $response =  get_parsed_validation_error_response($validator);
            return response()->json($response, 400);
        }else{
            try{
                $data = $request->only('location_id','type');

                $url = env('SEARCH_API_URL')."/tips/list";

                $data['currency'] = 'USD';
                $data['lang'] = "en-US";

                $host = env('SEARCH_API_HOST');
                $key = env('SEARCH_API_KEY');

                $headers = [ 'Content-Type: application/json',
                    'x-rapidapi-host: '.$host,
                    'x-rapidapi-key: '.$key ];
                $method = "GET";
                $results = callAPI($method, $url, $data , $headers);
                $parsed_data = parse_tips_data($results);

                $response = [
                    'status' => true,
                    'data' => $parsed_data
                ];
            } catch (\Exception $e) {
                // dd($e);
                $response = error_reponse_handler($e);
                return response()->json($response['response'],$response['status_code']);
            }
        }

        return response()->json($response,200);
    }

    /**
     * @api {get} /search  Search movie or tv show
     * @apiName Search movie or tv show
     * @apiGroup Search
     *
     * @apiHeader {String} Authorization='bearer bd970a05-0ec1-4412-8b28-657962f0f778'
     * @apiParam {Number} group_id selected group id (pass zero for solo)
     * @apiParam {String} genre  comma separated genre ids
     * @apiParam {String} network  networks id
     * @apiParam {String} type  movie,tv
     * @apiParam {String} [deadline] required for group search
     * @apiParam {String} search_day user meet/event date 2012-05-23
     * @apiParam {String} search_time user meet/event time 22:00
     * @apiParam {String} search_title search title
     * @apiParam {Number} [page]  page no
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *  {
     *     "status": "1",
     *     "data": {
     *         "page": 1,
     *         "total": 10000,
     *         "total_pages": 500,
     *         "results": [
     *             {
     *                 "image": "/5myQbDzw3l8K9yofUXRJ4UTVgam.jpg",
     *                 "id": 429617,
     *                 "genres": [
     *                     "Drama",
     *                     "Action & Adventure",
     *                     "Sci-Fi & Fantasy"
     *                 ],
     *                 "title": "Spider-Man: Far from Home",
     *                 "popularity": 86.042,
     *                 "vote_average": 7.5,
     *                 "vote_count": 7529,
     *                 "overview": "Peter Parker and his friends go on a summer trip to Europe. However, they will hardly be able to rest - Peter will have to agree to help Nick Fury uncover the mystery of creatures that cause natural disasters and destruction throughout the continent.",
     *                 "release_date": "2019-06-28"
     *             }...]
     *        }
     *  }
     *
     */
    public function search(Request $request){
        $response = ['status' => false, 'message' => 'Something Went wrong while processing'];
        $validator = Validator::make($request->all(), [
            'group_id' => 'required|numeric',
            'genre' => 'required',
            'network' => 'required',
            'type' => 'required|in:movie,tv',
            'deadline' => 'required|date_format:Y-m-d H:i:s',
            'search_day' => 'required|date|date_format:Y-m-d',
            'search_title' => 'required',
            'search_time' => 'required|date_format:H:i'
        ]);

        if ($validator->fails()) {
            $response =  get_parsed_validation_error_response($validator);
            return response()->json($response, 400);
        }else{
            try{
                $user = JWTAuth::toUser($request->token);

                if($user){

                    $data = $request->only('group_id','genre','network','page','type','deadline','search_day','search_time','search_title');


                    if(!isset($data['page'])){
                        $data["page"] = 1;
                    }

                    if($data['group_id'] != 0 && !isset($data['deadline'])){
                        $response['message'] =  'Deadline not provided';
                        return response()->json($response, 400);
                    }

                    $timestamp = strtotime($data['search_day']);
                    $search_day = date('w', $timestamp);

                    $search_time = explode(":",$data['search_time']);
                    $search_in_mins = ( $search_time[0] * 60) + $search_time[1];


                    $data['api_key'] =  env('API_KEY');
                    $data['language'] = "en-US";
                    $data['sort_by'] = "popularity.desc";
                    $data['include_adult'] = false;
                    $data['include_video'] = false;
                    $data['page'] =  $data['page'];

                    $data['with_genres'] =  $data['genre'];
                    $data['with_networks'] =  $data['network'];

                    if($data['type'] == "movie"){
                        $url = env('MOVIE_API_URL');
                    }else{
                        $url = env('TV_API_URL');
                    }

                    $results = callAPI('GET', $url, $data);

                    $result_data = [];
                    $image_url_prefix = env('API_IMG_PREFIX');
                    $page = $results->page;
                    $total_results = $results->total_results;
                    $total_pages = $results->total_pages;

                    $all_genres =  $this->genreRepository->findMultipleFromArray([
                        'type' => $data['type']
                    ]);

                    $all_genres_arr = [];
                    foreach($all_genres as $genre){
                        $all_genres_arr[$genre->genre_id] =  $genre->name;
                    }

                    foreach($results->results as $result){
                        $arr = [];
                        $image = 0;
                        if(!empty($result->poster_path) &&  $result->poster_path !=null ){
                            $image = 1;
                            $arr['image'] = $image_url_prefix.$result->poster_path;
                            $result->poster_path = $image_url_prefix.$result->poster_path;
                        }
                        if(!empty($result->backdrop_path) &&  $result->backdrop_path !=null ){
                            $image = 1;
                            $arr['image'] = $image_url_prefix.$result->backdrop_path;
                            $result->backdrop_path = $image_url_prefix.$result->backdrop_path;
                        }

                        if($image == 1){
                            $arr['id'] =  $result->id;
							$arr['genres'] =  $all_genres_arr[$data['genre']];

                            if($data['type'] == "movie"){
                                $arr['title'] =  $result->title;
                            }else{
                                $arr['title'] =  $result->name;
                            }

                            $arr['popularity'] =  $result->popularity;

                            $arr['vote_average'] =  $result->vote_average;
                            $arr['vote_count'] =  $result->vote_count;

                            $arr['overview'] =  $result->overview;

                            if($data['type'] == "movie"){
                                $arr['release_date'] =  $result->release_date;
                            }else{
                                $arr['first_air_date'] =  $result->first_air_date;
                            }

                            $result_data[] = $arr;
                        }
                    }

                    if($data['group_id'] == 0){
                        //solo search
                        $data['group_ids'] = [];
                    }else{
                        // $data['group_ids'] = explode(",",$data['group_id']);
                        $data['group_ids'] = [$data['group_id']];
                    }

                    // $parsed_data = get_parsed_search_response($search);
                    $compulsory_likes = config('constant.compulsory_likes');

                      //save results in database for future reference

                    if(!empty($result_data)){
                         $search = $this->searchTransactionRepository->save_search_results($user, $results, $data);
                        $response = [
                            'status' => true,
                            "message" => "Successful",
							'search_id' => $search->id
                            /*"data" => [
                                'compulsory_likes' => $compulsory_likes,
                                'search_id' => $search->id,
                                'search_title' => $search->search_title,
                                'deadline' => isset($data['deadline'])? $data['deadline'] :'',
                                'page' => $page,
                                'total' => $total_results,
                                'total_pages' => $total_pages,
                                'search_results' => $result_data
                            ]*/
                        ];
                    }else{
                         $response = [
                            'status' => false,
                            "message" => "Search not found"
                        ];
                    }

                }
            } catch (\Exception $e) {
                $response = error_reponse_handler($e);
                return response()->json($response['response'],$response['status_code']);
            }
        }
        return response()->json($response,200);
    }


    /**
     * api {get} /genre/get  Get genre
     * @apiName Get genre
     * @apiGroup Search
     *
     * @apiHeader {String} Authorization='bearer bd970a05-0ec1-4412-8b28-657962f0f778'
     * @apiParam {String} type  movie,tv
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *  {
     *     "status": "1",
     *     "data": []
     *  }
     *
     */
    public function genre(Request $request){
        $response = ['status' => false, 'message' => 'Something Went wrong while processing'];
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:movie,tv'
        ]);

        if ($validator->fails()) {
            $response =  get_parsed_validation_error_response($validator);
            return response()->json($response, 400);
        }else{
            try{
                $user = JWTAuth::toUser($request->token);

                if($user){
                    $data = $request->only('type');
                    $genres =  $this->genreRepository->findMultipleFromArray([
                        'type' => $data['type']
                    ]);
                    $response = [
                        'status' => true,
                        'data' => $genres
                    ];
                }
            } catch (\Exception $e) {
                $response = error_reponse_handler($e);
                return response()->json($response['response'],$response['status_code']);
            }
        }
        return response()->json($response,200);

    }


    /**
     * api {get} /streaming_provider/get  Get streaming_provider
     * @apiName Get streaming_provider
     * @apiGroup Search
     *
     * @apiHeader {String} Authorization='bearer bd970a05-0ec1-4412-8b28-657962f0f778'
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *  {
     *     "status": "1",
     *     "data": []
     *  }
     *
     */
    public function streaming_provider(Request $request){
        $response = ['status' => false, 'message' => 'Something Went wrong while processing'];
        try{
            $user = JWTAuth::toUser($request->token);
            if($user){
                $networks =  $this->streamingProviderRepository->all();
                $response = [
                    'status' => true,
                    'data' => [
                        'category' => [
                            'movie' => 'Movies',
                            'tv' => 'Tv Shows'
                        ],
                        'networks' => $networks
                    ]
                ];
            }
        } catch (\Exception $e) {
            $response = error_reponse_handler($e);
            return response()->json($response['response'],$response['status_code']);
        }
        return response()->json($response,200);
    }

        /**
     * @api {get} /search/get  Get Search Results
     * @apiName Get Search Results
     * @apiGroup Search
     *
     * @apiHeader {String} Authorization='bearer bd970a05-0ec1-4412-8b28-657962f0f778'
     * @apiParam {String} search_id  search_id
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *  {
     *     "status": "1",
     *     "data": {
     *         "results": [
     *             {
     *                 "photo": "https://media-cdn.tripadvisor.com/media/photo-o/14/97/93/09/sweet-dream-a-potpourri.jpg",
     *                 "location_string": "Pattaya, Chonburi Province",
     *                 "num_reviews": "1162",
     *                 "name": "Casa Pascal Restaurant",
     *                 "location_id": "1130181",
     *                 "longitude": "100.87983",
     *                 "latitude": "12.928686",
     *                 "cuisine": "Thai",
     *                 "address": "485/4 Moo 10, Second Road Opposite Royal Garden Plaza, Pattaya 20260 Thailand",
     *                 "write_review": "https://www.tripadvisor.com/UserReview-g293919-d1130181-Casa_Pascal_Restaurant-Pattaya_Chonburi_Province.html",
     *                 "web_url": "https://www.tripadvisor.com/Restaurant_Review-g293919-d1130181-Reviews-Casa_Pascal_Restaurant-Pattaya_Chonburi_Province.html",
     *                 "description": "Happy Dining. Setting culinary standards in Pattaya since 2001. In this oasis of Pattaya we cook the food with freshest ingredients, most of them seasonal and from local and imported sources.",
     *                 "price": "$10 - $30",
     *                 "rating": "4.5",
     *                 "distance_string": "1.1 mi",
     *                 "ranking": "#24 of 1,300 Restaurants in Pattaya",
     *                 "website": "http://www.restaurant-in-pattaya.com/",
     *                 "phone": "+66 61 643 9969"
     *             }],
     *          "compulsory_likes": 5
     *      }
     *  }
     *
     */

    public function get_countries(){
        $response = ['status' => false, 'message' => 'Something Went wrong while processing'];
        $validator = Validator::make($request->all(), [
            'search_id' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            $response =  get_parsed_validation_error_response($validator);
            return response()->json($response, 400);
        }else{
            try{
                $data = $request->only('search_id');
                //check search_id exists
                $search = $this->searchTransactionRepository->find($data['search_id']);
                if($search){

                    $parsed_data = get_parsed_search_response($search);
                    $compulsory_likes = config('constant.compulsory_likes'); //will be 20

                    $response = [
                        'status' => true,
                        'data' => [
                            'compulsory_likes' => $compulsory_likes,
                            'search_id' => $search->id,
                            'search_title' => $search->search_title,
                            'deadline' => isset($search->deadline)? $search->deadline :'',
                            'search_results' => ['results' => $parsed_data]
                        ]
                    ];
                }else{
                    $response['message'] = "Search not found";
                    return response()->json($response,'404');
                }

            } catch (\Exception $e) {

                $response = error_reponse_handler($e);
                return response()->json($response['response'],$response['status_code']);
            }
        }
        return response()->json($response,'200');
    }

    /**
     * @api {get} /movie/get  Get movie| tv details
     * @apiName Get movie or TV details
     * @apiGroup Search
     *
     * @apiHeader {String} Authorization='bearer bd970a05-0ec1-4412-8b28-657962f0f778'
     * @apiParam {String} type  movie,tv
     * @apiParam {Number} id  movie id, tv id
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *  {
     *     "status": "1",
     *     "data": []
     *  }
     *
     */
    public function get_movie_detail(Request $request){
          $response = ['status' => false, 'message' => 'Something Went wrong while processing'];
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:movie,tv',
            'id' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            $response =  get_parsed_validation_error_response($validator);
            return response()->json($response, 400);
        }else{
            try{
                $data = $request->only('id','type');
                   if($data['type'] == "movie"){
                        $url = env('MOVIE_DETAIL_API_URL').$data['id'];

                    }else{
                        $url = env('TV_DETAIL_API_URL').$data['id'];
                    }
                    $today =date('Y-m-d H:i:s');

                    $data['api_key'] =  env('API_KEY');
                    $data['language'] = "en-US";
                    $data['append_to_response'] = "videos,release_dates";
					$image_url_prefix = env('API_IMG_PREFIX');
                    $results = callAPI('GET', $url, $data);

					if(!empty($results->poster_path) &&  $results->poster_path !=null ){
						$results->poster_path = $image_url_prefix.$results->poster_path;
					}
					if(!empty($results->backdrop_path) &&  $results->backdrop_path !=null ){
						$results->backdrop_path = $image_url_prefix.$results->backdrop_path;
					}

					$search_transaction_details = $this->searchTransactionRepository->findMultipleFromArray([
                    ['matched_entity_id', '=', $results->id],
                    ]);
                    if(!empty($search_transaction_details)){
                        foreach($search_transaction_details as $search){
                            $users_members = [];

                            if((date("Y-m-d", strtotime($search->meet_time))) > $today){
                                //get any user who are not available or skipped the event
                                $skippedUsers = $this->searchTransactionUserRepository->findMultipleFromArray([
                                    'search_transaction_id' => $search->id
                                ])->toArray();
                                //get users attending to event
                                $users_members[] = get_search_users_attending_event($search, $skippedUsers);
                            }
                        }
                    }

                    $unique_members = array();
                    if(!empty($users_members)){

                        foreach($users_members as $array) {
                            foreach($array as $k=>$v) {
                                $unique_members[$k] = $v;
                            }
                        }
                    }
                     $response = [
                    'status' => true,
                    'data' => [
                        'search_detail' => $results,
                        'users_coming' => $unique_members
                    ]
                ];

            }
            catch (\Exception $e) {

                $response = error_reponse_handler($e);
                return response()->json($response['response'],$response['status_code']);
            }
        }
        return response()->json($response,'200');


    }

    /**
     * @api {delete} /search/delete  Delete Search
     * @apiName Delete search
     * @apiGroup Search
     *
     * @apiHeader {String} Authorization='bearer bd970a05-0ec1-4412-8b28-657962f0f778'
     * @apiParam {Number} search_id  search id
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *   {
     *     "status": true,
     *     "message": "Successfully Deleted"
     * }
     */
    public function remove_search(Request $request){
        $response = ['status' => false, 'message' => 'Something Went wrong while processing'];
        $validator = Validator::make($request->all(), [
            'search_id' => 'required',
        ]);

        if ($validator->fails()) {
            $response =  get_parsed_validation_error_response($validator);
            return response()->json($response, 400);
        }else{
            try{
                $user = JWTAuth::toUser($request->token);
                if($user){
                    $data = $request->only('search_id');
                    $resp = $this->searchTransactionRepository->remove_search($data['search_id']);
                    if($resp){
                        $response = [
                            'status' => true,
                            'message' => 'Successfully deleted Search',
                        ];
                    }
                }
            }catch(\Exception $e) {
                $response = error_reponse_handler($e);
                return response()->json($response['response'],$response['status_code']);
            }
        }
        return response()->json($response,200);
    }
}