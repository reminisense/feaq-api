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

// User Profile API routes
$app->get('user/{user_id}', 'UserController@fetchProfile');

$app->get('branch', 'BranchController@getTest');

//Authentication routes
$app->post('user/register', 'AuthenticationController@register');

$app->post('login', 'AuthenticationController@login');

$app->get('logout', 'AuthenticationController@logout');

//Dashboard routes
$app->get('business/search-suggest/{keyword}', 'BusinessController@searchSuggest');

$app->get('business/search', 'BusinessController@search');

//Broadcast routes

$app->get('advertisements/{business_id}', 'AdvertisementController@advertisements');

$app->post('queue/insert-number', 'QueueController@insertSpecific');

// Landing Page API
$app->post('business/search', 'LandingPageController@search');

$app->put('user/update', 'UserController@updateUser');

// Analytics API
//$app->get('analytics/business/{business_id}/{date_start}/{date_end}', 'AdminController@getBusinessnumbers');

// Admin API
$app->get('admin/list', 'AdminController@getAdmins');
$app->post('admin/add/{email}', 'AdminController@addAdmin');
$app->delete('admin/delete/{email}', 'AdminController@removeAdmin');
$app->get('admin/watchdog/{keyword}', 'AdminController@keyword');
$app->get('admin/features/{business_id}', 'AdminController@getBusinessFeatures');
$app->post('admin/features/update/{business_id}', 'AdminController@postSaveFeatures');
$app->get('admin/stats/{start_date}/{end_date}', 'AdminController@getBusinessnumbers');
$app->post('admin/show-graph', 'AdminController@showGraph');

// Layouts and Ads API
$app->post('ads/upload/{business_id}', 'AdvertisementController@postUploadImage');
$app->put('broadcast/update/{business_id}', 'BroadcastController@saveSettings');
$app->get('analytics/business/{business_id}/{start_date}/{end_date}', 'BusinessController@businessAnalytics');