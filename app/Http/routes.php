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

$app->get('branch', 'BranchController@getTest');

//Authentication routes
$app->post('user/register', 'AuthenticationController@register');

$app->post('login', 'AuthenticationController@login');

$app->get('logout', 'AuthenticationController@logout');

//Dashboard routes
$app->get('business/search-suggest/{keyword}', 'BusinessController@searchSuggest');

$app->get('business/search', 'BusinessController@search');


//Broadcast routes
$app->get('broadcast/{business_id}', 'BroadcastController@show');

$app->get('advertisements/{business_id}', 'AdvertisementController@advertisements');

$app->post('queue/insert-number', 'QueueController@insertSpecific');