<?php

namespace App\Http\Controllers\Feedback;


use App\Http\Controllers\Controller;

class FeedbackController extends Controller
{
    function index()
    {
        return view('feedback');
    }
}
