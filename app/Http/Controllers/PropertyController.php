<?php

namespace App\Http\Controllers;

// Модели
use App\Property;


use Illuminate\Http\Request;

class PropertyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $property = Property::with('units_category.units')->findOrFail(3);

        // dd($property);
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
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

    // ---------------------------------------------- Ajax ----------------------------------------------------------

    public function add_property(Request $request)
    {
        $property = Property::with('units_category.units')->findOrFail($request->id);

        if ($property) {

            if (isset($property->units_category->name)) {
                $units_list = $property->units_category->units->pluck('abbreviation', 'id');
            } else {
                $units_list = null;
            }
            // echo $property;
            
            return view($request->entity.'.metrics.add-property', ['type' => $property->type, 'units_list' => $units_list, 'property_id' => $request->id]);
        } else {
            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при добавлении свойства!'
            ];
        }
    }
}
