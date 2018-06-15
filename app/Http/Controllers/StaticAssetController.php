<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StaticAssetController extends Controller
{
    public function langJs()
    {
        $lang = config('app.locale');
        $langFiles = glob(resource_path("lang/$lang/*.php"));

        $trans = [];
        foreach($langFiles as $langFile)
        {
            $basename = basename($langFile, '.php');
            $trans[$basename] = require $langFile;
        }

        $result = escape_json_string_js_printable(json_encode($trans));

        return response()
            ->make("window.lang = JSON.parse(\"$result\");")
            ->header('Content-Type', 'text/javascript');
    }
}
