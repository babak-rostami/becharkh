<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class BabakController extends Controller
{

    function babak(): View
    {
        return view('babak');
    }
}
