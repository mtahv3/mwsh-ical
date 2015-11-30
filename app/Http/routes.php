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
    return view('index');
    //return $app->welcome();
});

$app->get('schedule/league/{leagueId:[\d]+}/team/{teamId:[\d]+}', 'QHL\ScheduleController@getSchedule');
$app->get('league', 'QHL\LeagueController@getLeagues');
$app->get('league/{leagueId:[\d]+}/teams', 'QHL\LeagueController@getTeams');
$app->get('cache/flush', 'QHL\CacheController@clearCache');