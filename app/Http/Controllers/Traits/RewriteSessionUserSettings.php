<?php

namespace App\Http\Controllers\Traits;

trait RewriteSessionUserSettings
{


    // Отдает нужное название метода для отправки на проверку права
	public function RSUserSettings($key, $value){

	    // ========================== Перезапись сессии начало - обновление UserConditions ==========================

	    // Получаем сессию
		$conditions  = session('conditions');



	    // Пишем в сессию измененное состояние чего либо
		$conditions['conditions'][$key] = $value;



	    // Перезаписываем сессию
		session(['conditions' => $conditions]);

		return true;

	    // ========================== Перезапись сессии конец - обновление UserConditions ==========================

	}

}