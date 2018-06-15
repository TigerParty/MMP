<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;

class ManagementController extends Controller {

    public function main()
    {
        return view('management');
    }
}
