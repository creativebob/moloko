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
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // Инфо о странице
        $pageInfo = pageInfo($this->entity_name);

        return view('home', compact('pageInfo'));
    }
}
