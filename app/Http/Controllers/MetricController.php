<?php

namespace App\Http\Controllers;

// Модели
use App\Metric;
use App\Value;

use DB;

use Illuminate\Http\Request;

class MetricController extends Controller
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
        // echo $request->values[0];
        // foreach ($request->values as $value) {
        //     echo $value;
        // }


        // Получаем данные для авторизованного пользователя
        $user = $request->user();
        $company_id = $user->company_id;

        // Скрываем бога
        $user_id = hideGod($user);

        $metric = new Metric;
        $metric->company_id = $company_id;
        $metric->property_id = $request->property_id;
        $metric->name = $request->name;
        $metric->description = $request->description;

        if ($request->type == 'numeric' || $request->type == 'percent') {
            $metric->min = $request->min;
            $metric->max = $request->max;
            $metric->unit_id = $request->unit_id;
        }

        $metric->author_id = $user_id;

        $metric->save();

        if ($request->type == 'list') {
            $values = [];

            foreach ($request->values as $value) {

                $values[] = [
                    'metric_id' => $metric->id,
                    'value' => $value,
                    'author_id' => $user_id,
                    'company_id' => $company_id,
                ];     
            } 

            $lol = Value::insert($values);
            // echo json_encode($values);

           //  $metric->values()->createMany($values);

           // $metric->values()->saveMany([
           //      foreach ($request->values as $value) {

           //          new Value ([
           //              'value' => $value,
           //              'author_id' => $user_id,
           //              'company_id' => $company_id,
           //          ]),     
           //      } 
           //  ]);
        }

        

        if ($metric) {


            // echo $metric;
            // Переадресовываем на получение метрики
            return redirect()->action('MetricController@add_metric', ['id' => $metric->id, 'entity_id' => $request->product_id, 'entity' => $request->entity]);
        } else {
            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при добавлении свойства!'
            ];
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $metric = Metric::with('values')->findOrFail($id);
        dd($metric);
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



    // --------------------------------------------- Ajax -------------------------------------------------

    public function add_metric(Request $request)
    {

        $metric = Metric::with('unit')->findOrFail($request->id);

        // Смотрим с какой сущностью нужно связать метрику
        switch ($request->entity) {
            case 'products':
            $metric->products()->toggle([$request->entity_id => ['entity' => $request->entity]]);
            break;
        }


        return view($request->entity.'.metric', ['metric' => $metric]);
    }

    public function delete_metric(Request $request)
    {

        $metric = Metric::findOrFail($request->id);

        // Смотрим от какой сущности нужно отвязать метрику
        switch ($request->entity) {
            case 'products':
            $res = $metric->products()->toggle([$request->entity_id => ['entity' => $request->entity]]);
            break;
        }

        if ($res) {
            $result = [
                'error_status' => 0,
            ];
        } else {
            $result = [
                'error_message' => 'Не удалось удалить метрику!',
                'error_status' => 1,
            ];
        }
        
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
    }

}
