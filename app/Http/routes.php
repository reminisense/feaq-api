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
$app->get('broadcast/{raw_code}', 'BroadcastController@fetchDetails');

// Advertisement API routes
$app->get('advertisement/{business_id}', 'AdvertisementController@fetchImages');

// IssueNumber API routes
$app->post('issuenumber/insert-specific', 'IssueNumberController@postInsertSpecific');

// User Profile API routes
$app->get('user/{user_id}', 'UserController@fetchProfile');
$app->post('user/update', 'UserController@updateUser');