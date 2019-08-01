<?php

namespace App\Http\Controllers;

use App\EstimatesItem;
use Illuminate\Http\Request;

class EstimatesItemController extends Controller
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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
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
     * @param  \App\EstimatesItem  $estimatesItem
     * @return \Illuminate\Http\Response
     */
    public function show(EstimatesItem $estimatesItem)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\EstimatesItem  $estimatesItem
     * @return \Illuminate\Http\Response
     */
    public function edit(EstimatesItem $estimatesItem)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\EstimatesItem  $estimatesItem
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, EstimatesItem $estimatesItem)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\EstimatesItem  $estimatesItem
     * @return \Illuminate\Http\Response
     */
    public function destroy(EstimatesItem $estimatesItem)
    {
        //
    }


    public function ajax_edit(Request $request)
    {

        // Получаем авторизованного пользователя
        $user = $request->user();

        $user_id = hideGod($user);
        $company_id = $user->company_id;

        $estimate_item = EstimatesItem::findOrFail($request->id);

        return view('leads.pricing.pricing-modal', compact('estimate_item'));

    }
}
