<?php

namespace App\Http\Controllers\Traits\Widgets;
use App\Lead;
use App\User;

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

        $leads = Lead::whereNotIn('stage_id', [13, 14, 1, 12])->whereNull('draft')->pluck('manager_id');
        $managers = $leads->unique()->toArray();
        // dd($managers);

        $users = User::withCount(['leads' => function($q) {
            $q->whereNotIn('stage_id', [13, 14, 1, 12])->whereNull('draft');
        },
        'challenges_work', 
        'challenges_today',
        'challenges_tomorrow',
        'challenges_week',
        'challenges_future', 
        'challenges_last',
        // 'leads_without_challenges',
        'badget'])
        ->whereIn('id', $managers)
        ->orderBy('leads_count', 'desc')
        ->get();
        // dd($users);

        $lead_work = [];

        foreach ($users as $user) {

            $badget = $user->leads->whereNotIn('stage_id', [13, 14, 1, 12])->where('draft', null)->sum('badget');

            $lead_work[$user->name_reverse] = [
                'leads_work' => $user->leads_count,
                'leads_without_challenges' => 0,
                'leads_badget' => $badget,
                'challenges_last_percent' => ($user->challenges_count > 0) ? $user->challenges_last_count * 100 / $user->challenges_count : 0,
                'challenges_work_count' => $user->challenges_work_count,
                'challenges_last_count' => $user->challenges_last_count,
                'challenges_today_count' => $user->challenges_today_count,
                'challenges_tomorrow_count' => $user->challenges_tomorrow_count,
                'challenges_week_count' => $user->challenges_week_count,
                'challenges_future_count' => $user->challenges_future_count,

            ];

        }


        // dd($lead_work);

        $result['data'] = $lead_work;

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