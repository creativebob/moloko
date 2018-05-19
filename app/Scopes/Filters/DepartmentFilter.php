<?php

namespace App\Scopes\Filters;

trait DepartmentFilter
{
    // Фильтрация по отделу
    public function scopeDepartmentFilter($query, $request, $relations = null)
    {
        // Проверка на существование
        if($request->department_id){


            // ПРЯМОЙ ЗАПРОС
            // --------------------------------------------------------------------------------------------------------------------------------------
            if($relations == null){



                if($request->department_id == [0 => 'null']){

                    $query = $query->WhereNull('department_id');
                } else 
                {
                    $query = $query->whereIn('department_id', $request->department_id)->orWhereNull('department_id');
                };



            // ЗАПРОС ЧЕРЕЗ СВЯЗЬ
            // --------------------------------------------------------------------------------------------------------------------------------------
            } else {

                // Если приходит массив только с нулем (Требуеться выбрать только записи без города)
                if($request->department_id == [0 => 'null']){

                    $query = $query->WhereNull('staffer_id');
                } else 
                {

                    // Если приходит массив с нулем и другими ID
                    if(in_array('null', $request->department_id)){

                        $query = $query->whereHas('staffer', function ($query) use ($request) {
                            $query = $query->whereIn('department_id', $request->department_id);
                        })->orWhereNull('staffer_id');

                    // Если массив приходит без нуля
                    } else {


                        $query = $query->whereHas('staffer', function ($query) use ($request) {
                            $query = $query->whereIn('department_id', $request->department_id);
                        });
                    };

                };

            };
        };

        return $query;
    }

}
