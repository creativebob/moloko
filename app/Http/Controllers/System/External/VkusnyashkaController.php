<?php

namespace App\Http\Controllers\System\External;

use App\Http\Controllers\Controller;

class VkusnyashkaController extends Controller
{
    /**
     * RollHouseController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

}
