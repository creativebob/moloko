<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    // Сущность над которой производит операции контроллер
    protected $entity_name = 'dashboard';

    public function index()
    {
        // Инфо о странице
        $page_info = pageInfo($this->entity_name);
        
        
        
        return view('dashboard', compact('page_info'));
    }
}
