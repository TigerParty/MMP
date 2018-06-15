<?php
namespace App\Http\Api\App\V4;

abstract class ApiController
{
    abstract public function download($version);
}
