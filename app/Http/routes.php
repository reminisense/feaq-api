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
$app->post('user/register', 'AuthenticationController@register');

$app->post('login', 'AuthenticationController@login');

$app->get('logout', 'AuthenticationController@logout');

//Business API routes
$app->get('business/search-suggest/{keyword}', 'BusinessController@searchSuggest');

$app->get('business/search', 'BusinessController@search');
$app->get('business/{business_id}', 'BusinessController@getDetails');
//Broadcast routes

$app->get('advertisements/{business_id}', 'AdvertisementController@advertisements');

$app->post('queue/insert-number', 'QueueController@insertSpecific');

// Landing Page API
$app->post('business/search', 'LandingPageController@search');

$app->put('user/update', 'UserController@updateUser');

// Analytics API
$app->get('analytics/business/{business_id}/{start_date}/{end_date}', 'BusinessController@businessAnalytics');//$app->get('analytics/business/{business_id}/{date_start}/{date_end}', 'AdminController@getBusinessnumbers');

// Admin API
$app->get('admin/list', 'AdminController@getAdmins');
$app->post('admin/add/{email}', 'AdminController@addAdmin');
$app->delete('admin/delete/{email}', 'AdminController@removeAdmin');
$app->get('admin/watchdog/{user_id}/{keyword}/', 'WatchdogController@getUserdata');
$app->get('admin/features/{business_id}', 'AdminController@getBusinessFeatures');
$app->post('admin/features/update/{business_id}', 'AdminController@postSaveFeatures');
$app->get('admin/stats/{start_date}/{end_date}', 'AdminController@getBusinessnumbers');
$app->post('admin/show-graph', 'AdminController@getProcessnumbers');

// Layouts and Ads API
$app->post('ads/upload/{business_id}', 'AdvertisementController@postUploadImage');
$app->put('broadcast/update/{business_id}', 'BroadcastController@saveSettings');

// RESTFUL services routes
$app->post('services', 'ServiceController@postCreateService');

$app->put('services/{id}', 'ServiceController@putUpdateService');

$app->delete('services/{id}', 'ServiceController@deleteRemoveService');

// RESTFUL terminals routes
$app->post('terminals/user', 'TerminalController@postAddUser');

$app->delete('terminals/user/{terminal_id}/{user_id}', 'TerminalController@deleteRemoveUser');

$app->post('terminals', 'TerminalController@postCreateTerminal');

$app->put('terminals/{id}', 'TerminalController@putUpdateTerminalName');

$app->delete('terminals/{id}', 'TerminalController@deleteRemoveTerminal');

//RESTFUL queue routes
$app->get('queue/numbers/{terminal_id}', 'QueueController@getAllNumbers');

$app->put('queue/serve', 'QueueController@putServeNumber');

$app->put('queue/drop', 'QueueController@putDropNumber');

$app->put('queue/call', 'QueueController@putCallNumber');

$app->post('queue/insert-multiple', 'QueueController@postIssueMultiple');

$app->post('queue/user/rating', 'QueueController@postUserRating');$app->delete('business/delete', 'BusinessController@deleteRecord');
$app->put('business/update', 'BusinessController@putUpdate');
