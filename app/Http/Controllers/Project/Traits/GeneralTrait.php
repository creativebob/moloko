<?php

namespace App\Http\Controllers\Project\Traits;

// Модели
use App\Navigation;
use App\Staffer;
use App\Department;


use Illuminate\Support\Facades\Cache;

trait GeneralTrait
{

	// Графики
	public function department(){

    	// dd('lol');
        // $company = Cache::rememberForever(env('SITE_DOMAIN').'-company', function() {
		// $staffer = Staffer::with(['department.location.city', 'filial.location.city', 'company.location.city'])->first();

		// if (isset($staffer->company->location)) {
		// 	$address = $staffer->company->location;
		// }

		// if (isset($staffer->filial->location)) {
		// 	$address = $staffer->filial->location;
		// }

		// if (isset($staffer->department->location)) {
		// 	$address = $staffer->department->location;
		// }

		$department = Department::with('location.city')->find(1);

        // });
        // dd($department);
		return $department;

	}
    // Графики
	public function graphics(){

		$staff = Staffer::with(['department.schedules.worktimes', 'filial.schedules.worktimes', 'company.schedules.worktimes', 'schedules.worktimes', 'user'])->orderBy('sort', 'asc')->find([1, 2]);


		$graphics = [];
		foreach ($staff->toArray() as $staffer) {

			$item = null;

			if (isset($staffer->company->schedules)) {
				$item = $staffer->company->schedules;
			}

			if (isset($staffer->filial->schedules)) {
				$item = $staffer->filial->schedules;
			}

			if (isset($staffer->department->schedules)) {
				$item = $staffer->department->schedules;
			}

			if (isset($staffer->schedules)) {
				$item = $staffer->schedules;
			}

			$graphics[$staffer['id']] = $staffer;
			$graphics[$staffer['id']]['schedule'] = $item['schedules'][0];

			foreach ($staffer['schedules'][0]['worktimes'] as $key => $value) {
				$graphics[$staffer['id']]['schedule']['days'][$value['weekday']]['worktime_begin'] = secToTime($value['worktime_begin']);
				$graphics[$staffer['id']]['schedule']['days'][$value['weekday']]['worktime_end'] = secToTime($value['worktime_begin'] + $value['worktime_interval']);
			}
		}
        // dd($graphics);
		return $graphics;
	}

    // Навигации
	public function navigations(){

        // Получаем общую инфу сайта
        // $navigations = Cache::rememberForever(env('SITE_DOMAIN').'-navigations', function() {
		$navigations = Navigation::with(['navigations_category', 'menus' => function ($query) {
			$query->with('page')->whereDisplay(1)->orderBy('sort', 'asc');
		}])->whereSite_id(2)->get()->keyBy('navigations_category.tag');
        // });

		return $navigations;
	}
}