<?php

namespace App\Http\Controllers;

use App\Shift;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    protected $entityAlias;
    protected $entityDependence;

    /**
     * ShiftController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->entityAlias = 'shifts';
        $this->entityDependence = true;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Shift::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        $shifts = Shift::with([
            'filial',
            'outlet'
        ])
            ->moderatorLimit($answer)
            ->companiesLimit($answer)
            ->authors($answer)
            ->systemItem($answer)
//            ->filter()
//            ->orderBy('moderation', 'desc')
            ->oldest('created_at')
            ->paginate(30);
//        dd($subscribers);

        // Инфо о странице
        $pageInfo = pageInfo($this->entityAlias);

        return view('system.pages.shifts.index', compact('shifts', 'pageInfo'));
    }
}
