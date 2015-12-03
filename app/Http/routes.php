<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$app->get('/', function () use ($app) {
    return $app->welcome();
});

// Broadcast API routes
$app->get('broadcast/{raw_code}', 'BroadcastController@getDetails');

// Advertisement API routes
$app->get('advertisement/{business_id}', 'AdvertisementController@getImages');

// Queue API routes
$app->post('queue/insert-specific', 'QueueController@postInsertSpecific');
$app->post('queue/insert-number', 'QueueController@insertSpecific');

// User Profile API routes
$app->get('user/{user_id}', 'UserController@fetchProfile');
$app->post('user/register', 'AuthenticationController@register');
$app->get('branch', 'BranchController@getTest');
$app->put('user/update', 'UserController@updateUser');

//Authentication routes
$app->post('login', 'AuthenticationController@login');
$app->get('logout', 'AuthenticationController@logout');

//Business API routes
$app->get('business/search-suggest/{keyword}', 'BusinessController@searchSuggest');
$app->get('business/search', 'BusinessController@search');
$app->get('business/{business_id}', 'BusinessController@getDetails');
$app->post('business/search', 'LandingPageController@search');