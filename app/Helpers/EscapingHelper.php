<?php

/*
|--------------------------------------------------------------------------
| Custom JSON String Escaper
|--------------------------------------------------------------------------
|
| Escaping PHP string to able to pring a json string inside Javascript file
| Javascript should use JSON.parse() to decode this escaped JSON string
|
| Javascript use double quote to warp the escaped string:
|      Json.pares("{print escaped string here}")
*/


if (!function_exists('escape_json_string_js_printable')) {
    function escape_json_string_js_printable($JsonString)
    {
        $escapers = array("\\", "/", "\"", "\n", "\r", "\t", "\x08", "\x0c");
        $replacements = array("\\\\", "\\/", "\\\"", "\\n", "\\r", "\\t", "\\f", "\\b");
        return str_replace($escapers, $replacements, $JsonString);
    }
}
