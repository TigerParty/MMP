<?php
/*
|--------------------------------------------------------------------------
| Custom Argo path helper register
|--------------------------------------------------------------------------
|
| Same as Laravl origin helpers
| All function here will register as helper functions into global scope
| Only do this for non object oriented functions
|
| Refer: /vender/laravel/framework/illuminate/Support/helper.php
*/

if (!function_exists('argo_image_path')) {
    /*
    |--------------------------------------------------------------------------
    | Generate image path by attachment id
    |--------------------------------------------------------------------------
    |
    | Can transfer nullable $attachmentId into correct impage path
    | Considering fallback image setting in config file
    |
    */
    function argo_image_path($attachmentId = null, $defaultPath = null)
    {
        if ($attachmentId) {
            return url("/file/$attachmentId");
        } elseif ($defaultPath) {
            return url($defaultPath);
        } elseif (config('argo.fallback_image', false)) {
            return url(config('argo.fallback_image'));
        }

        return url('/images/default_logo.png');
    }
}
