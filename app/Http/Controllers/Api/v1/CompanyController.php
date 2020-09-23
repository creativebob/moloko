<?php

namespace App\Http\Controllers\Api\v1;

use App\Company;
use App\Http\Controllers\Controller;
use App\LegalForm;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Поиск компании по имени с исключением правовой формы
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchByName(Request $request)
    {

        $legalFormsList = LegalForm::get()
            ->pluck('name', 'id');

        $name = $request->name;
        foreach ($legalFormsList as $key => $value) {

            if (preg_match("/(^|\s)" . $value . "\s/i", $request->name, $matches)) {
                $name = str_replace($matches[0], "", $request->name);
            }
        }

        $company = Company::where('name', 'like', $name  . '%')
            ->get();

        return response()->json($company);
    }
}
