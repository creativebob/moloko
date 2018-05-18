<?php

namespace App\Scopes\Filters;

trait Filter
{
    // Фильтрация по должности
    public function scopeFilter($query, $request, $column, $relations = null)
    {
        // Принимаем значения и создаем переменные: имя поля и имя связующей сущности
        $column_id = $column . '_id';
        $relations_id = $relations . '_id';

        // Проверка на существование
        if($request->$column_id){


            // ПРЯМОЙ ЗАПРОС
            // --------------------------------------------------------------------------------------------------------------------------------------
            if($relations == null){

                if($request->$column_id == [0 => 'null']){

                    $query = $query->WhereNull($column_id);
                } else 
                {
                    $query = $query->whereIn($column_id, $request->$column_id)->orWhereNull($column_id);
                };



            // ЗАПРОС ЧЕРЕЗ СВЯЗЬ
            // --------------------------------------------------------------------------------------------------------------------------------------
            } else {

                // Если приходит массив только с нулем (Требуеться выбрать только записи без города)
                if($request->$column_id == [0 => 'null']){

                    $query = $query->WhereNull($relations_id);
                } else 
                {

                    // Если приходит массив с нулем и другими ID
                    if(in_array('null', $request->$column_id)){

                        $query = $query->whereHas($relations, function ($query) use ($request, $column_id) {
                            $query = $query->whereIn($column_id, $request->$column_id);
                        })->orWhereNull($relations_id);

                    // Если массив приходит без нуля
                    } else {


                        $query = $query->whereHas($relations, function ($query) use ($request, $column_id) {
                            $query = $query->whereIn($column_id, $request->$column_id);
                        });
                    };

                };

            };
        };

        return $query;
    }

}
