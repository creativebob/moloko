<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Сущность над которой производит операции контроллер
    protected $entity_name = 'home';

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Инфо о странице
        $page_info = pageInfo($this->entity_name);
        
        return view('home', compact('page_info'));
    }
}
