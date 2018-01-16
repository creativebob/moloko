<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HelpController extends Controller
{
    public function show_session()
    {
        return view('show_session');
    }
}
