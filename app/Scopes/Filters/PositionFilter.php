<?php

namespace App\Scopes\Filters;

trait PositionFilter
{
    // Фильтрация по должности
    public function scopePositionFilter($query, $request, $relations = null)
    {

        // Проверка на существование
        if($request->position_id){


            // ПРЯМОЙ ЗАПРОС
            // --------------------------------------------------------------------------------------------------------------------------------------
            if($relations == null){



                if($request->position_id == [0 => 'null']){

                    $query = $query->WhereNull('position_id');
                } else 
                {
                    $query = $query->whereIn('position_id', $request->position_id)->orWhereNull('position_id');
                };



            // ЗАПРОС ЧЕРЕЗ СВЯЗЬ
            // --------------------------------------------------------------------------------------------------------------------------------------
            } else {

                // Если приходит массив только с нулем (Требуеться выбрать только записи без города)
                if($request->position_id == [0 => 'null']){

                    $query = $query->WhereNull('staffer_id');
                } else 
                {

                    // Если приходит массив с нулем и другими ID
                    if(in_array('null', $request->position_id)){

                        $query = $query->whereHas('staffer', function ($query) use ($request) {
                            $query = $query->whereIn('position_id', $request->position_id);
                        })->orWhereNull('staffer_id');

                    // Если массив приходит без нуля
                    } else {


                        $query = $query->whereHas('staffer', function ($query) use ($request) {
                            $query = $query->whereIn('position_id', $request->position_id);
                        });
                    };

                };

            };
        };

        return $query;
    }

}
