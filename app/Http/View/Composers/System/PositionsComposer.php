<?php

namespace App\Http\View\Composers\System;

use App\Position;
use Illuminate\View\View;

class PositionsComposer
{
	public function compose(View $view)
	{
		$answer = operator_right('positions', false, 'index');

		$positions = Position::moderatorLimit($answer)
            ->companiesLimit($answer)
            ->authors($answer)
            ->systemItem($answer)
            ->where('archive', false)
    //		->template($answer)
            ->get();

		return $view->with(compact('positions'));

		  //       // Смотрим на наличие должности в данном филиале, в массиве устанавливаем id должностей, которых не может быть более 1ой
				// $direction = Staffer::where(['position_id' => 1, 'filial_id' => $filial_id])->moderatorLimit($answer_staff)->count();

				// $repeat = [];

				// if ($direction == 1) {
				// 	$repeat[] = 1;
				// }

		  //       // Получаем из сессии необходимые данные (Функция находиться в Helpers)
				// $answer_positions = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

		  //       // -------------------------------------------------------------------------------------------
		  //       // ГЛАВНЫЙ ЗАПРОС
		  //       // -------------------------------------------------------------------------------------------
				// $positions_list = Position::with('staff')->moderatorLimit($answer_positions)
				// ->companiesLimit($answer_positions)
		  //       ->filials($answer_positions) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
		  //       ->authors($answer_positions)
		  //       ->systemItem($answer_positions) // Фильтр по системным записям
		  //       ->template($answer_positions) // Выводим шаблоны в список
		  //       ->whereNotIn('id', $repeat)
		  //       ->pluck('name', 'id');
    }

}
