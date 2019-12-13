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
    public function index(Request $request)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_properties = operator_right('properties', false, 'index');
        $answer_metrics = operator_right('metrics', false, 'index');

        $entity_id = $request->entity_id;

        $properties = Property::moderatorLimit($answer_properties)
            ->companiesLimit($answer_properties)
            ->authors($answer_properties)
            ->systemItem($answer_properties)
            ->template($answer_properties)
            ->with(['metrics' => function ($query) use ($answer_metrics, $entity_id) {
                $query->with('values')
                    ->moderatorLimit($answer_metrics)
                    ->companiesLimit($answer_metrics)
                    ->authors($answer_metrics)
                    ->systemItem($answer_metrics)
                    ->whereHas('entities', function($q) use ($entity_id) {
                        $q->where('id', $entity_id);
                    });
            }])
            ->withCount('metrics')
            ->orderBy('sort', 'asc')
            ->get();

        return response()->json($properties);
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
        $property = Property::with('units_category.units')
        ->findOrFail($request->id);

        if ($property) {

            if (isset($property->units_category->name)) {
//                $units_list = $property->units_category->units->pluck('abbreviation', 'id');
                $units_list = $property->units_category->units;
            } else {
                $units_list = null;
            }
            // echo $property;

            return response()->json([
                'type' => $property->type,
                'units' => $units_list,
            ]);

//            return view('products.common.metrics.add_property', [
//                'type' => $property->type,
//                'units_list' => $units_list,
//                'property_id' => $request->id,
//                'entity' => $request->entity,
//                'set_status' => $request->set_status
//            ]);
        } else {
            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при добавлении свойства!'
            ];
        }
    }
}
