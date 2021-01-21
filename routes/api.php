<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {

});

Route::group(['prefix'=> 'v1'],function(){
    Route::post('register', 'Api\ApiController@register','user.register');
    Route::post('login', 'Api\ApiController@login','user.login');
    Route::post('verify_access_code', 'Api\ApiController@verify_access_code','user.verify');
    Route::get('get-countries', 'Api\ApiController@get_countries');
    Route::post('user/test', 'Api\ApiController@testPushNotification');
    Route::post('credits/add', 'Api\UserController@credits_add');
    Route::post('favourite/add', 'Api\UserController@favourite_add');
    Route::post('favourite/get', 'Api\UserController@favourite_get');
    Route::post('favourite/delete', 'Api\UserController@favourite_delete');
});

//search function
    Route::group(['middleware' => ['jwt.verify'], 'prefix' => 'v1', 'namespace' => 'Api'], function (){
        Route::post('search', 'ActivityController@search');
        // Route::post('uploadImage', 'ActivityController@uploadImage');
        Route::post('movie_shows', 'ActivityController@movie_shows');
        Route::post('addExperiences', 'ActivityController@addExperiences');
        Route::post('viewExperiences', 'ActivityController@viewExperiences');
        // Route::post('shareExperiences', 'ActivityController@shareExperiences');
        Route::post('sharedExperiences', 'ActivityController@sharedExperiences');
        Route::post('leaveOrDeleteExperience', 'ActivityController@leaveOrDeleteExperience');
        // Route::post('userExperiences', 'ActivityController@userExperiences');
        Route::post('userSources', 'ActivityController@userSources');
        Route::post('addUserSources', 'ActivityController@addUserSources');
        Route::post('addUserExperience','ActivityController@addUserExperience');
        Route::post('getUserExperience','ActivityController@getUserExperience');
        Route::post('leave_delete_experience','ActivityController@leave_delete_experience');
    });
//search function

Route::group(['prefix'=> 'v1',  'middleware' => ['jwt.verify']],function () {
    Route::get('home', 'Api\UserController@home');
    Route::post('logout', 'Api\ApiController@logout');
    Route::get('user/search', 'Api\UserController@search');
    Route::post('add/premium_users', 'Api\UserController@add_premium_users');
    Route::post('search_Option/get', 'Api\UserController@get_search_option');

    Route::get('user/profile/get', 'Api\UserController@get_profile_details');
    Route::post('user/profile/update', 'Api\UserController@update_profile_details');

    Route::get('user/favorite/get', 'Api\UserController@get_favorites');
    Route::post('user/favorite/add', 'Api\UserController@add_favorite');
    Route::delete('user/favorite/delete', 'Api\UserController@remove_favorite');

    Route::get('user/feedback/get', 'Api\UserController@get_feedback');
    Route::post('user/feedback/add', 'Api\UserController@add_feedback');

    Route::post('user/skip/add', 'Api\UserController@add_skip_user');

    Route::post('group/create', 'Api\GroupController@create');
    Route::post('group/update', 'Api\GroupController@update');
    Route::delete('group/delete', 'Api\GroupController@delete');

    Route::post('group/exit', 'Api\GroupController@exit_group');

    Route::post('group/member/add', 'Api\GroupController@add_member');
    Route::delete('group/member/delete', 'Api\GroupController@delete_member');

    Route::get('group/all', 'Api\GroupController@get_all_groups');
    Route::get('group/member/all', 'Api\GroupController@get_group_members');

    Route::get('search', 'Api\SearchController@search');
    Route::get('search/get', 'Api\SearchController@search_results');
    Route::get('genre/get', 'Api\SearchController@genre');
    Route::get('streaming_provider/get', 'Api\SearchController@streaming_provider');
    Route::delete('search/delete', 'Api\SearchController@remove_search');


    Route::get('location/search_filters', 'Api\SearchController@search_filters');
    Route::get('location/search_matched_pending', 'Api\SearchController@search_matched_pending');

    Route::get('location/search', 'Api\SearchController@location_search');
    Route::post('search/save_solo_match', 'Api\SearchController@save_solo_match');

    Route::get('location/get', 'Api\SearchController@get_location_detail');
    Route::get('location/get_tips', 'Api\SearchController@get_location_tips');

    Route::post('location/like_dislike', 'Api\SearchController@like_dislike_search');

    Route::get('crew/member/get', 'Api\GroupController@get_crew_members');
    Route::get('movie/get', 'Api\SearchController@get_movie_detail');

    Route::get('get_date_ideas', 'Api\DateIdeaController@getDateIdeas');
    Route::get('get_date_idea_details/{id}', 'Api\DateIdeaController@getDateIdeaDetail');

    Route::post('save_date_idea_likes', 'Api\DateIdeaController@saveDateIdeaLike');
});