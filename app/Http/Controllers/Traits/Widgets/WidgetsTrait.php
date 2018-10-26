<?php

namespace App\Http\Controllers\Traits\Widgets;
use App\User;
use App\Department;

trait WidgetsTrait
{

    public function addWidgets($widgets){

    	// Инициируем контейнер виджета, если он пуст

    	foreach ($widgets as $widget_tag) {

    		switch ($widget_tag) {
    			case 'sales-department-burden':
    				$this->salesDepartmentBurden();
    				break;

    			case 'marketing-info':
    				$this->marketingInfo();
    				break;
    			
    			default:
    				# code...
    				break;
    		}

    	}

    	// Пишем в контейнер дополнительные настройки
    	// Количество виджетов
    	// $this->widgets_total['count'] = count($this->widgets_total);

	}



	// --------------------------------------------------------------------------------------------------------
	// ВИДЖЕТ: НАГРУЗКА НА ОТДЕЛ ПРОДАЖ -----------------------------------------------------------------------
	// --------------------------------------------------------------------------------------------------------

	public function salesDepartmentBurden(){

		// Формируем информацию о виджете
		$result['widget'] = null;

		// Формируем данные для виджета
		$result['data'] = null;

        $this->widgets_total['sales-department-burden'] = $result;
	}

	// --------------------------------------------------------------------------------------------------------



	// --------------------------------------------------------------------------------------------------------
	// ВИДЖЕТ: ИНФОРМАЦИЯ О МАРКЕТИНГНЕ -----------------------------------------------------------------------
	// --------------------------------------------------------------------------------------------------------

	public function marketingInfo(){

		// Формируем данные для виджета
		$result = Department::first();

        $this->widgets_total['marketing-info'] = $result;
	}

	// --------------------------------------------------------------------------------------------------------


}