<?php

namespace App\Http\Controllers\Traits;
use App\Department;

trait RewriteSessionDepartments
{


    // Отдает нужное название метода для отправки на проверку права
    public function RSDepartments($user){

	    // ========================== Перезапись сессии начало - обновление Departments ==========================

	    // Получаем сессию
	    $access  = session('access');

	        // Получаем все отделы компании
	        $my_departments = Department::whereCompany_id($user->company_id)->get();

	        // Настройка прав бога, если он авторизован под компанией
	        foreach($my_departments as $my_department){

	            // Пишем в сессию список отделов
	            $access['company_info']['departments'][$my_department->id] = $my_department->name;

	            // Пишем в сессию список филиалов
	            if($my_department->parent_id == null){
	                $access['company_info']['filials'][$my_department->id] = $my_department->name;
	            };
	        }

	    // Перезаписываем сессию
	    session(['access' => $access]);

	    return true;

	    // ========================== Перезапись сессии конец - обновление Departments ==========================

	}

}