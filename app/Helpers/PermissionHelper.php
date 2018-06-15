<?php

use App\Argo\PermissionLevel;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Custom Argo permission helper register
|--------------------------------------------------------------------------
|
| Same as Laravel origin helpers
| All function here will register as helper functions into global scope
| Only do this for non object oriented functions
|
| Refer: /vender/laravel/framework/illuminate/Support/helper.php
*/

/*
|--------------------------------------------------------------------------
| Get current permission level from Session->Model->Config
|--------------------------------------------------------------------------
|
| Return current permission level priority value
|
*/
if (!function_exists('argo_current_permission')) {
    function argo_current_permission()
    {
        if (session()->has('identity')) {
            return (int)session('identity');
        } elseif (Auth::check()) {
            $identity = (int)PermissionLevel::find(Auth::user()->permission_level_id)->priority;
            session()->put('id', $identity);
            return $identity;
        } else {
            $identity = (int)config('argodf.default_view_priority');
            session()->put('id', $identity);
            return $identity;
        }
    }
}

/*
|--------------------------------------------------------------------------
| Return boolean of access authorize from session or database
|--------------------------------------------------------------------------
|
| Get entity permission priority as input
| Return boolean of access authorization
|
*/
if (!function_exists('argo_is_accessible')) {
    function argo_is_accessible($entityIdentity)
    {
        $currentIdentity = argo_current_permission();
        return $currentIdentity <= (int)$entityIdentity;
    }
}

/*
|--------------------------------------------------------------------------
| Return boolean of accessibility of Admin permission from config
|--------------------------------------------------------------------------
|
| Return boolean of admin access authorization
|
*/
if (!function_exists('argo_is_admin_accessible')) {
    function argo_is_admin_accessible()
    {
        $currentIdentity = argo_current_permission();
        $adminIdentity = config('argodf.admin_function_priority');
        return $currentIdentity <= $adminIdentity;
    }
}
