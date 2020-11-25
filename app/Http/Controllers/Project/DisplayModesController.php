<?php

namespace App\Http\Controllers\Project;

use App\DisplayMode;
use App\Http\Controllers\Controller;

class DisplayModesController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return response()->json(DisplayMode::get());
    }
}
