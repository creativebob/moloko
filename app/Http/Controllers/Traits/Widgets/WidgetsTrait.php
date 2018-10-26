<?php

namespace App\Http\Controllers\Traits\Widgets;
use App\Lead;

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
		$result['widget_info'] = $this->all_widgets['sales-department-burden'];

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('leads', true, 'index');

        $leads = Lead::with([
            'manager',
            'stage',
            'challenges.challenge_type',
            'challenges.appointed'
        ])
        // ->withCount(['challenges' => function ($query) {
        //     $query->whereNull('status');
        // }])
        ->whereNotIn('stage_id', [13, 14, 1, 12])
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->filials($answer) 
        ->manager($this->request->user)
        ->whereNull('draft')
        ->systemItem($answer) // Фильтр по системным записям
        ->orderBy('created_at', 'desc')
        ->get()
        ->groupBy('manager.name');


		// $leads_ready = $leads->where('stage')


        // dd($leads);

		// $result['data'] = 

		// dd($result['data']);
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