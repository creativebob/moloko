<?php

namespace App\Scopes\Filters;

trait DepartmentFilter
{
    // Фильтрация по городу
    public function scopeDepartmentFilter($query, $request, $relations = null)
    {

    	if($relations == null){

	        //Фильтруем по списку городов
	        if($request->department_id){
	          $query = $query->whereIn('department_id', $request->department_id);
	        };

    	} else {

	        if($request->department_id){
	        	// dd($request->position_id);

				$query = $query->whereHas('staffer', function ($query) use ($request) {
				    $query = $query->whereIn('department_id', $request->department_id);
				})->orWhereNull('staffer_id');
		    };

    	};

      return $query;
    }

}
