<?php

namespace App\Scopes\Filters;

trait Filter
{
    // Фильтрация по должности
    public function scopeFilter($query, $request, $column, $relations = null)
    {
        // Принимаем значения и создаем переменные: имя поля и имя связующей сущности
        // $column_id = $column . '_id';
        $relations_id = $relations . '_id';

        // Проверка на существование
        if($request->$column){


            // ПРЯМОЙ ЗАПРОС
            // --------------------------------------------------------------------------------------------------------------------------------------
            if($relations == null){

                if($request->$column == [0 => 'null']){

                    $query = $query->WhereNull($column);
                } else 
                {
                    $query = $query->whereIn($column, $request->$column)->orWhereNull($column);
                };



            // ЗАПРОС ЧЕРЕЗ СВЯЗЬ
            // --------------------------------------------------------------------------------------------------------------------------------------
            } else {


                $array_relations = explode(".", $relations);

                if(count($array_relations) > 1){

                $relations_1 = $array_relations[0];
                $relations_2 = $array_relations[1];
                $relations_id = $relations_2 . '_id';


                    // Если приходит массив только с нулем (Требуеться выбрать только записи без города)
                    if($request->$column == [0 => 'null']){

                        $query = $query->WhereNull($relations_id);
                    } else 
                    {

                        // Если приходит массив с нулем и другими ID
                        if(in_array('null', $request->$column)){

                            $query = $query->whereHas($relations_1, function ($query) use ($request, $column, $relations_2, $relations_id) {
                                $query = $query->whereHas($relations_2, function ($query) use ($request, $column, $relations_id) {
                                    $query = $query->whereIn($column, $request->$column);
                                })->orWhereNull($relations_id);
                            })->orWhereNull($relations_id);

                        // Если массив приходит без нуля
                        } else {

                            $query = $query->whereHas($relations_1, function ($query) use ($request, $column, $relations_2) {
                                $query = $query->whereHas($relations_2, function ($query) use ($request, $column) {
                                    $query = $query->whereIn($column, $request->$column);
                                });
                            });
                        };

                    };




                } else {

                    // Если приходит массив только с нулем (Требуеться выбрать только записи без города)
                    if($request->$column == [0 => 'null']){

                        $query = $query->WhereNull($relations_id);
                    } else 
                    {

                        // Если приходит массив с нулем и другими ID
                        if(in_array('null', $request->$column)){

                            $query = $query->whereHas($relations, function ($query) use ($request, $column) {
                                $query = $query->whereIn($column, $request->$column);
                            })->orWhereNull($relations_id);

                        // Если массив приходит без нуля
                        } else {


                            $query = $query->whereHas($relations, function ($query) use ($request, $column) {
                                $query = $query->whereIn($column, $request->$column);
                            });
                        };

                    };

                };

            };
        };

        return $query;
    }

}
