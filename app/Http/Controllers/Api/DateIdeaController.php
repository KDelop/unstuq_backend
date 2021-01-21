<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\DateIdea\DateIdeaRepository;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;


class DateIdeaController extends Controller
{
    private $dateIdeaRepository;

    public function __construct(DateIdeaRepository $dateIdeaRepository)
    {
        $this->dateIdeaRepository = $dateIdeaRepository;
    }


    /**
     * @api {get} /get_date_ideas Date Idea Data
     * @apiName Get Date Idea
     * @apiGroup DateIdea
     *
     * @apiHeader {String} Authorization='bearer bd970a05-0ec1-4412-8b28-657962f0f778'
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     * {
     *       "status": true,
     *       "data":[
     *         {
     *          "id": 10,
     *          "title": "Hike at sunrise/sunset",
     *          "image": "",
     *          "likes": 1,
     *          "user_like_status": 1
     *          },
     *          {
     *           "id": 7,
     *           "title": "Play racquetball or tennis",
     *           "image": "",
     *           "likes": 0,
     *           "user_like_status": 0
     *          }
     *     ]
     *       "message": "Date Idea List"
     *  }
     *
     */
    public function getDateIdeas(Request $request)
    {
        $response = ['status' => false, 'message' => 'Something Went wrong while processing'];
        try {
            $user = JWTAuth::toUser($request->token);
            if($user) {
                $dateIdeas = $this->dateIdeaRepository->get_date_ideas($user->id);
                $response = [
                    'status' => true,
                    'data' => $dateIdeas,
                    'message' => 'Date Idea list.',
                ];
            }
        } catch (\Exception $e) {
            $response = error_reponse_handler($e);
            return response()->json($response['response'],$response['status_code']);
        }
        return response()->json($response,200);
    }

    /**
     * @api {get} /get_date_idea_details/{id} Get Date Idea Details
     * @apiName Get Date Idea Details
     * @apiGroup DateIdea
     *
     * @apiHeader {String} Authorization='bearer bd970a05-0ec1-4412-8b28-657962f0f778'
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     * {
     *       "status": true,
     *       "data":{
     *            "id": 3,
     *            "title": "Break a sweat jumping trampoline",
     *            "description": "\"Visit an extreme trampoline park. It is a lot of fun and a great way to show off\"",
     *            "image": "",
     *            "likes": 6,
     *            "instructions": "",
     *            "submitted_by": "",
     *            "created_at": "2020-09-29 10:39:04",
     *            "category": "",
     *            "difficulty": 0,
     *            "user_like_status": 1
     *     }
     *       "message": "Date Idea Details"
     *  }
     *
     */
    public function getDateIdeaDetail(Request $request, $id)
    {
        $response = ['status' => false, 'message' => 'Something Went wrong while processing'];
        try {
            $user = JWTAuth::toUser($request->token);
            if($user) {
                $dateIdea = $this->dateIdeaRepository->get_date_idea($user->id, $id);
                $response = [
                    'status' => true,
                    'data' => $dateIdea,
                    'message' => 'Date Idea Details',
                ];
            }
        } catch (\Exception $e) {
            $response = error_reponse_handler($e);
            return response()->json($response['response'],$response['status_code']);
        }
        return response()->json($response,200);
    }

    /**
     * @api {post} /save_date_idea_likes Store Date Idea Like
     * @apiName Store Date Idea Likes
     * @apiGroup DateIdea
     *
     * @apiHeader {String} Authorization='bearer bd970a05-0ec1-4412-8b28-657962f0f778'
     * @apiParam  {Number} date_idea_id  data idea id.
     * @apiParam  {Number} user_id  user id.
     * @apiParam  {Number} status status 1/0.
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     * {
     *       "status": true,
     *       "message": "Date Idea Like Successfully Added"
     *  }
     *
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 400 Not Found
     *     {
     *        "status": false,
     *        "message": "The date idea id field is required., The user id field is required., The status field is required."
     *     }
     *
     */
    public function saveDateIdeaLike(Request $request)
    {
        $response = ['status' => false, 'message' => 'Something Went wrong while processing'];
        $validator = Validator::make($request->all(), [
            'date_idea_id' => 'required',
            'user_id' => 'required',
            'status' => 'required',
        ]);

        if ($validator->fails()) {
            $response =  get_parsed_validation_error_response($validator);
            return response()->json($response, 400);
        } else {
            try {
                $user = JWTAuth::toUser($request->token);
                if($user) {
                    $this->dateIdeaRepository->save_date_idea_like($request);
                    $response = [
                        'status' => true,
                        'message' => 'Date Idea Like Successfully Added',
                    ];
                }

            } catch (\Exception $e) {
                $response = error_reponse_handler($e);
                return response()->json($response['response'], $response['status_code']);
            }
        }
        return response()->json($response,200);
    }
}
