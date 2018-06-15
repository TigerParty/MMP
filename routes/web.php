<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['prefix' => 'static', 'middleware' => ['cache.browser']], function(){
    Route::get('lang.js', 'StaticAssetController@langJs');
});


/*
|--------------------------------------------------------------------------
| Management Features
|--------------------------------------------------------------------------
|
| Backend with Admin namespace
| Frontend implement by React JS via Webpack
|
*/
Route::group([
    'prefix'     => '/management',
    'middleware' => ['auth', 'admin', 'cache.config'],
    'namespace'  => 'Management',
], function () {
    Route::get('/', 'ManagementController@main');

    // User Management Tool
    Route::post('/user/query', 'UserController@query');
    Route::post('/user/', 'UserController@store');
    Route::get('/user/{id}', 'UserController@show');
    Route::put('/user/{id}', 'UserController@update');
    Route::delete('/user/{id}', 'UserController@destroy');
    Route::post('/user/{id}/switch_notify', 'UserController@switchNotify');

    Route::post('/project/query', 'ProjectController@query');

});

Route::group([
    'prefix'     => '/admin',
    'middleware' => ['auth', 'admin', 'cache.config'],
    'namespace'  => 'Admin',
], function () {
    Route::get('/', 'AdminController@main');

    //-- Project Management
    Route::post('/project/query', 'ProjectController@query');
    Route::put('/project/{id}/approval', 'ProjectController@updateApproval');
    Route::post('/project/{id}/status', 'ProjectController@updateStatus');

    Route::delete('/project/{id}', 'ProjectController@destroy');
});

Route::group([
    'prefix'     => '/feedback',
    'middleware' => ['cache.config'],
    'namespace'  => 'Feedback',
], function () {
    Route::get('/', "FeedbackController@index");

    Route::get('sms/api', "CitizenSMSController@smsIndexApi");
    Route::get('sms/{id}/api', "CitizenSMSController@smsShowApi");
    Route::delete('sms/{id}/api', "CitizenSMSController@smsDeleteApi");
    Route::put('sms/{id}/mark_read/api', "CitizenSMSController@smsMarkReadApi");
    Route::post('sms_reply', "CitizenSMSController@smsReplyStoreApi");

    Route::get('report/api', "CitizenReportController@reportIndexApi");
    Route::get('report/{id}/api', "CitizenReportController@reportShowApi");
    Route::delete('report/{id}/api', "CitizenReportController@reportDeleteApi");
    Route::put('report/{id}/mark_read/api', "CitizenReportController@reportMarkReadApi");

    Route::get('voice/api', "VoiceController@voiceIndexApi");
    Route::get('voice/{surveyId}/api', "VoiceController@voiceShowApi");

    Route::get('comment/api', "CommentController@commentIndexApi");
    Route::get('comment/{entityID}/api', "CommentController@commentShowApi");
    Route::post('comment', "CommentController@commentStoreApi");

    Route::group(['middleware' => 'admin'], function() {
        Route::put('comment/{commentID}/mark_read/api', "CommentController@commentMarkReadApi");
        Route::post('comment/{commentID}/reply', "CommentController@commentReplyStoreApi");
        Route::delete('comment/{commentID}', "CommentController@commentDeleteApi");
    });
});


Route::group(['middleware' => ['cache.config']], function () {
    /*
    |--------------------------------------------------------------------------
    | Authorized routes
    |--------------------------------------------------------------------------
    |
    | Routes which only accessible by logged in users
    |
    */
    Route::group(['middleware' => 'auth'], function () {
        /*
        |--------------------------------------------------------------------------
        | Legacy Admin Features
        |--------------------------------------------------------------------------
        |
        | Routes which can only be accesse by Admin role in config
        |
        */
        Route::group([
            'prefix'     => '/admin',
            'middleware' => 'admin'
        ], function () {

            Route::post('/project/api', 'ProjectAdminController@indexQueryApi');

            Route::get('/project/create', 'ProjectAdminController@create');
            Route::get('/project/create/api', 'ProjectAdminController@createApi');
            Route::post('/project', 'ProjectAdminController@store');

            Route::get('/project/create/batch', 'ProjectAdminController@batch');
            Route::get('/project/create/batch/api', 'ProjectAdminController@batchApi');

            Route::get('/project/excel', 'ProjectAdminController@indexExcel');
            Route::get('/project/{parentProjectId}/container/{containerId}/subproject/excel', 'ProjectAdminController@indexExcel');

            Route::get('/project/{projectId}', 'ProjectAdminController@show');
            Route::get('/project/{projectId}/api', 'ProjectAdminController@showApi');

            Route::get('/project/{projectId}/edit', 'ProjectAdminController@edit');
            Route::get('/project/{projectId}/edit/api', 'ProjectAdminController@editApi');
            Route::put('/project/{projectId}', 'ProjectAdminController@update');

            Route::get('/project/{projectId}/form/{formId}/create', 'ProjectAdminController@formCreate');
            Route::get('/project/{projectId}/form/{formId}/create/api', 'ProjectAdminController@formCreateApi');
            Route::post('/project/{projectId}/form/{formId}', 'ProjectAdminController@formStore');

            Route::get('/project/{projectId}/form/{formId}/edit', 'ProjectAdminController@formEdit');
            Route::get('/project/{projectId}/form/{formId}/edit/api', 'ProjectAdminController@formEditApi');
            Route::put('/project/{projectId}/form/{formId}', 'ProjectAdminController@formUpdate');

            Route::get('/project/{projectId}/container/{containerId}/subproject/create', 'ProjectAdminController@create');
            Route::get('/project/{projectId}/container/{containerId}/subproject/create/api', 'ProjectAdminController@createApi');

            Route::post('/project/import/excel', 'ProjectAdminController@importExcel');

            Route::get('/project/{projectId}/container/{containerId}/subproject/create/batch', 'ProjectAdminController@batch');
            Route::get('/project/{projectId}/container/{containerId}/subproject/create/batch/api', 'ProjectAdminController@batchApi');

            //-- Status Management
            Route::get('/status', 'StatusController@index');
            Route::post('/status', 'StatusController@update');

            //-- Form Management
            Route::get('/dync_form/create', 'DyncFormController@create');
            Route::get('/dync_form/{df_id}/edit', 'DyncFormController@edit');
            Route::get('/dync_form', 'DyncFormController@index');
            Route::get('/dync_form/api', 'DyncFormController@indexApi');
            Route::get('/dync_form/{df_id}', 'DyncFormController@show');
            Route::get('/dync_form/{df_id}/delete', 'DyncFormController@destroy');
            Route::post('/dync_form', 'DyncFormController@store');
            Route::put('/dync_form/{df_id}', 'DyncFormController@update');

            //-- Notification Management
            Route::get('/notification', 'NotificationController@index');
            Route::post('/notification/add_receiver', 'NotificationController@store');
            Route::delete('/notification/{notification_id}', 'NotificationController@destroy');
            Route::get('/notification/api', 'NotificationController@indexApi');
            Route::post('/notification/api', 'NotificationController@indexQueryApi');

            Route::get('/notification/email/{notificationType}/{notifycationId}/api', 'NotificationController@showEmail');
            Route::post('/notification/email/{notificationType}/{notifycationId}', 'NotificationController@syncEmail');

            Route::get('/notification/sms/{notificationType}/{notifycationId}/api', 'NotificationController@showSMS');
            Route::post('/notification/sms/{notificationType}/{notifycationId}', 'NotificationController@syncSMS');

            //-- Card
            Route::get('/card/project/{projectId}', 'ProjectAdminController@showCard');
        });

        //-- Report
        Route::get('/project/{p_id}/report/create', 'ReportController@create');
        Route::post('/project/{p_id}/report', 'ReportController@store');
        Route::get('/project/{p_id}/report/{rp_id}/delete', 'ReportController@destroy');
        Route::get('/project/{projectId}/report', 'ProjectController@reportIndex');
    });

    //-- Home
    Route::get('/', 'HomeController@home');
    Route::get('/home/api', 'HomeController@homeApi');
    Route::get('/tutorial', 'HomeController@tutorial');
    Route::get('/sitemap', 'HomeController@showSitemap');

    //-- Explore Render
    Route::group(['prefix' => 'explore'], function () {
        Route::get('/', 'ExploreController@index');
        Route::get('/api', 'ExploreController@indexApi');
        Route::post('/queryApi', 'ExploreController@queryApi');
    });

    //-- Map
    Route::group(['prefix' => 'map'], function () {
        Route::post('/api/reporter_location', 'MapController@last_reporter_location');
        //show citizen report on explore page
        Route::post('/api/citizen_report', 'MapController@citizenReportApi');
        Route::get('/api/tracker', 'MapController@trackerApi');
    });

    //-- Project
    Route::get('/project/{projectId}', 'ProjectController@show');
    Route::get('/project/{projectId}/api', 'ProjectController@showApi');
    Route::get('/project/{projectId}/form/{formId}', 'ProjectController@showFormApi');
    Route::get('/project/{projectId}/container/{containerId}', 'ProjectController@showContainer');
    Route::get('/project/{projectId}/container/{containerId}/api', 'ProjectController@showContainerApi');
    Route::post('/project/{projectId}/container/{containerId}/api', 'ProjectController@queryContainerApi');
    Route::get('project/{projectId}/aggregation/api', 'ProjectController@aggregationIndexApi');

    //-- Indicator
    Route::get('/project/{projectId}/indicator/{indicatorId}/api', 'IndicatorController@showApi');

    //-- Syllabus
    Route::get('/syllabus', 'SyllabusController@index');
    Route::get('/syllabus/api', 'SyllabusController@indexApi');

    //-- Container
    Route::get('/container/{id}/api', 'ContainerController@showApi');

    //-- Chart
    Route::get('/featured_datas', 'HomeController@featured_datas');
    Route::get('/chart_gs', 'HomeController@chart_gs');

    //-- Report
    Route::get('/report/{rp_id}', 'ReportController@show');
    Route::get('/report/{rp_id}/api', 'ReportController@showApi');

    //-- Region
    Route::get('/region', 'RegionController@index');
    Route::get('/region/api', 'RegionController@indexRootApi');
    Route::get('/region/{regionId}', 'RegionController@show');
    Route::get('/region/{regionId}/api', 'RegionController@showApi');
    Route::get('/region/{regionId}/project/api', 'RegionController@projectIndexApi');

    //-- File
    Route::group(['prefix' => 'file'], function () {
        Route::get('/{ath_id}', 'AttachmentController@access');
        Route::get('/{ath_id}/download', 'AttachmentController@download');
        Route::get('/{ath_id}/data', 'AttachmentController@get_attach_data');
        Route::post('/upload', 'AttachmentController@upload');
        Route::post('/', 'AttachmentController@checkFile');
    });

    //-- Login
    Route::get('/login', 'LoginController@showLogin');
    Route::get('/logout', 'LoginController@doLogout');
    Route::post('/login', 'LoginController@doLogin');

    //-- Website API
    Route::post('/webapi/auto_merge_project_value', 'WebApiController@auto_merge_project_value');
    Route::post('/webapi/rotate', 'WebApiController@setRotateImage');
    Route::post('/webapi/districts_by_region', 'WebApiController@getDistrictsByRegion');

    //-- Comment
    Route::group(['prefix' => 'comment'], function () {
        Route::get('home', "HomeController@comment");
        Route::get('project/{projectID}', "ProjectController@comment");
    });
});
