<?php

namespace App\Http\Controllers;

use App\AgencyScheme;
use App\Entity;
use Illuminate\Http\Request;

class AgencySchemeController extends Controller
{

    protected $entityAlias;
    protected $entityDependence;

    public function __construct()
    {
        $this->middleware('auth');
        $this->entityAlias = 'agency_scheme';
        $this->entityDependence = false;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $data = $request->input();
        $agencyScheme = AgencyScheme::create($data);
        return response()->json($agencyScheme);
    }

    /**
     * Архивация указанного ресурса.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function archive($id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod('destroy'));

        // ГЛАВНЫЙ ЗАПРОС:
        $agencyScheme = AgencyScheme::moderatorLimit($answer)
            ->find($id);

        if (empty($agencyScheme)) {
            abort(403, __('errors.not_found'));
        }

        // Подключение политики
//        $this->authorize(getmethod('destroy'), $agencyScheme);

        $res = $agencyScheme->archive();
        return response()->json($res);
    }
}
