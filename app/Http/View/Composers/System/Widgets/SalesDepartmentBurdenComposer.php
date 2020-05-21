<?php

namespace App\Http\View\Composers\System\Widgets;

use App\Lead;
use App\User;
use Illuminate\View\View;

class SalesDepartmentBurdenComposer
{
	public function compose(View $view)
	{
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('leads', true, 'index');

        $leads = Lead::whereNotIn('stage_id', [13, 14, 1, 12])
            ->manager(request()->user())
            ->whereNull('draft')
            ->pluck('manager_id');

        $managers = $leads->unique()->toArray();
        // dd($managers);

        $users = User::withCount([
            'leads' => function($q) {
                $q->whereNotIn('stage_id', [13, 14, 1, 12])
                    ->whereNull('draft');
            },
            'challenges_work',
            'challenges_today',
            'challenges_tomorrow',
            'challenges_aftertomorrow',
            'challenges_week',
            'challenges_future',
            'challenges_last',
            'badget',
            'leads_without_challenges',
            'leads_cancel',
            'leads_control'
        ])
            ->whereIn('id', $managers)
            ->orderBy('leads_count', 'desc')
            ->get();

        // $my_leads = Lead::withCount(['leads' => function($q) {
        //     $q
        //     ->whereNotIn('stage_id', [13, 14, 1, 12])
        //     ->whereNull('draft');
        // },
        // 'budget'])
        // ->get();


        // dd($users);

        $lead_work = [];


        // $leads_without_challenges = Lead::whereNotIn('stage_id', [13, 14, 1, 12])
        // ->manager($this->request->user())
        // ->whereNull('draft')
        // ->where('challenges_active_count', 0)
        // ->get();

        // dd($leads_without_challenges);

        foreach ($users as $user) {

            $badget = $user->leads->whereNotIn('stage_id', [13, 14, 1, 12])->where('draft', null)->sum('badget');

            if($user->challenges_work_count > 0){
                $challenges_last_percent = $user->challenges_last_count * 100 / $user->challenges_work_count;
            } else {
                $challenges_last_percent = 0;
            }

            if($user->leads_control_count > 0){
                $leads_cancel_percent = $user->leads_cancel_count * 100 / $user->leads_control_count;
            } else {
                $leads_cancel_percent = 0;
            }

            // $leads_without_challenges = $user->leads->whereNotIn('stage_id', [13, 14, 1, 12])->where('draft', null)->where('challenges_active_count', 0)->count();

            // dd($user->leads->whereNotIn('stage_id', [13, 14, 1, 12])->where('draft', null)->where('challenges_active_count', 0)->first());
            // dd($leads_without_challenges);
            // dd($user->leads_without_challenges);

            $lead_work[$user->name_reverse] = [
                'leads_work' => $user->leads_count,
                'leads_without_challenges_count' => $user->leads_without_challenges_count,
                'leads_badget' => $badget,
                'challenges_last_percent' => $challenges_last_percent,
                'challenges_work_count' => $user->challenges_work_count,
                'challenges_last_count' => $user->challenges_last_count,
                'challenges_today_count' => $user->challenges_today_count,
                'challenges_tomorrow_count' => $user->challenges_tomorrow_count,
                'challenges_aftertomorrow_count' => $user->challenges_aftertomorrow_count,
                'challenges_week_count' => $user->challenges_week_count,
                'challenges_future_count' => $user->challenges_future_count,
                'leads_cancel_count' => $user->leads_cancel_count,
                'leads_control_count' => $user->leads_control_count,
                'leads_cancel_percent' => $leads_cancel_percent,
            ];

        }


        // dd($lead_work);

        $data['data'] = $lead_work;
        return $view->with(compact('data'));
	}
}
