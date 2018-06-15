<?php

use Illuminate\Http\Request;


// App v5
Route::group(['prefix' => '/v5', 'namespace' => 'App\V5'], function(){
    Route::get('/download/', 'AppController@download');
    Route::post('/submit', 'AppController@submit');
    Route::post('/file', 'AppController@uploadFile');
});

// App V4, the old Android App compatiblity
Route::group(['prefix' => '/v4', 'namespace' => 'App\V4'], function(){
    Route::get('/version', 'AppController@getCurrentVersion');
    Route::post('/version', 'AppController@compareVersion');
    Route::get('/download/{version}', 'AppController@download');
    Route::post('/submit/{version}', 'AppController@submit');
    Route::post('/reporter_location', 'AppController@submitReporterLocation');

});

// LiteApp V1
Route::group(['prefix' => '/lite/v1', 'namespace' => 'LiteApp\V1'], function(){
    Route::post('/submit', 'AppController@submit');
});

// ArgoGateway Api V1
Route::group(['prefix' => '/gateway/v1', 'namespace' => 'SmsGateway\V1'], function(){
    Route::get('/fetch', 'AppController@fetch');
    Route::post('/submit', 'AppController@submit');
});

// TrackerApp V1
Route::group(['prefix' => '/tracker/v1', 'namespace' => 'TrackerApp\V1'], function(){
    Route::get('/download', 'AppController@download');
    Route::post('/submit', 'AppController@submit');
    Route::post('/file', 'AppController@uploadFile');
});
