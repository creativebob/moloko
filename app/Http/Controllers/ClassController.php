<?php

namespace App\Http\Controllers;

use Bootstrap\Telegram\Commands as TelegramCommans;

class ClassController extends Controller
{
    
    
    public function check_class()
    {
        if (!class_exists('TelegramCommans')) {
            dd('нету');
        }
    }
    

}
