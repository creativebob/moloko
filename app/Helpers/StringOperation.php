<?php

    function decor_access_block($access_block) {
        if($access_block == 1){$result = "Блокирован";} else {$result = "Открыт";};
        return $result;
    };

    function decor_user_type($user_type) {
        if($user_type == 1){$result = "Сотрудник";} 
        elseif($user_type == 2){$result = "Клиент";} 
        else {$result = "Статус не определен";};
        return $result;
    };

	function cleanPhone($str) {
		$ptn = "/[^0-9]/";
		$rpltxt = "";
		$result = preg_replace($ptn, $rpltxt, $str);
		return $result;
	};

    function secToTime($sec) {
                $hour = floor($sec/3600);
                $sec = $sec - ($hour*3600);
                $min = floor($sec/60);
                $sec = $sec - ($min*60);
                if($hour < 10){$hour = '0' . $hour;}
                if($min < 10){$min = '0' . $min;}
                return $hour.":".$min;
    };

    function timeToSec($time) {

                $mass = explode(":", $time);  
                $hour = $mass[0];
                $min = $mass[1];
                $sec = $hour*60*60 + $min*60;

                return $sec;
    };

	/**
	 * @param  Принимает 11 значное число: номер телефона
	 * @return Возвращает строку: Удобочитаемый номер телефона со скобочками и дефисом
	 */
    function decorPhone($value) {
        
        if(strlen($value) == 11 ){
            if(mb_substr($value, 0, 4) == "8395"){
                $rest1 = mb_substr($value, 5, 2); // возвращает "abcd"
                $rest2 = mb_substr($value, 7, 2); // возвращает "abcd"
                $rest3 = mb_substr($value, 9, 2); // возвращает "abcd"
                $result = $rest1."-".$rest2."-".$rest3;
            } else {
                // $value = strtolower($value, "UTF-8");
                $rest1 = mb_substr($value, 0, 1); // возвращает "bcdef"
                $rest2 = mb_substr($value, 1, 3); // возвращает "bcd"
                $rest3 = mb_substr($value, 4, 3); // возвращает "abcd"
                $rest4 = mb_substr($value, 7, 2); // возвращает "abcdef"
                $rest5 = mb_substr($value, 9, 2); // возвращает "abcdef"
                $result = $rest1." (".$rest2.") ".$rest3."-".$rest4."-".$rest5;
            };
        };

        if(strlen($value) < 6){
            $result = "Номер не указан";
        };

        if(strlen($value) == 0){
            $result = NULL;
        };

        return $result;
    }


	// 	function date_to_mysql($ddd)
	// {
	// 		/*  Подстраиваем дату под календарь  */	
	// 		list($dd, $dm, $dy) = sscanf($ddd, "%d.%d.%d");
	// 		$ddd = $dy."-".$dm."-".$dd;
	// 		return $ddd;
	// };

        function date_to_mysql($value)
    {
            $date_parts = explode('.', $value);
            $result = $date_parts[2].'-'.$date_parts[1].'-'.$date_parts[0];
            return $result;
    }



        // Отдает нужное название метода для отправки на проверку права
        function getmethod($method){

        if($method == 'index'){return 'index';};
        if(($method == 'current_city')||($method == 'current_department')||($method == 'current_sector')){return 'index';};

        if($method == 'show'){return 'view';};
        if(($method == 'edit')||($method == 'update')){return 'update';};
        if(($method == 'create')||($method == 'store')){return 'create';};
        if($method == 'destroy'){return 'delete';};
        if($method == 'setting'){return 'update';};

         }


        // Отдает нужное название метода для отправки на проверку права
        function getMassArguments($request, $name_argument){

            if($request->server('argv') != null){
                $mass_arg = explode("&", $request->server('argv')[0]);
                foreach($mass_arg as $key=>$mystring){
                    $id_city = str_after($mystring, $name_argument . '=');
                    $mass_arg[$key] = $id_city;
                }
            } else {$mass_arg = null;};

            return $mass_arg;

        }



        // -----------------------------------------------------------------------------------------------
        // ПРИНИМАЕМ РАСПИСАНИЕ И ГОТОВИМ К ЗАПИСИ В БАЗУ ------------------------------------------------
        // -----------------------------------------------------------------------------------------------

        // Получает массив времени на неделю (начало/окончание расписания) полностью оформленный и готовый для записи 
        // через DB в таблицу worktimes. Функции нужны следующие аргументы:
        // $request  - запрос приложения
        // $schedule_id - ID расписания для которого сохраняеться время.
 
        function getWorktimes($request, $schedule_id){

            // Получаем из запроса все данные из полей расписания работы в один массив
            $schedule_mass[0]['worktime_begin'] = $request->mon_begin;
            $schedule_mass[0]['worktime_end'] = $request->mon_end;
            $schedule_mass[1]['worktime_begin'] = $request->tue_begin;
            $schedule_mass[1]['worktime_end'] = $request->tue_end;
            $schedule_mass[2]['worktime_begin'] = $request->wed_begin;
            $schedule_mass[2]['worktime_end'] = $request->wed_end;
            $schedule_mass[3]['worktime_begin'] = $request->thu_begin;
            $schedule_mass[3]['worktime_end'] = $request->thu_end;
            $schedule_mass[4]['worktime_begin'] = $request->fri_begin;
            $schedule_mass[4]['worktime_end'] = $request->fri_end;
            $schedule_mass[5]['worktime_begin'] = $request->sat_begin;
            $schedule_mass[5]['worktime_end'] = $request->sat_end;
            $schedule_mass[6]['worktime_begin'] = $request->sun_begin;
            $schedule_mass[6]['worktime_end'] = $request->sun_end;


            // Переводим указанное время в секунды (а также делаем кое-какие проверки и доп.операции) 
            // и сохраняем в другой массив.

            // Готовим массив
            $mass_time = [];

            // Перебираем каждый день недели
            for($n = 0; $n < 7; $n++){

                // Проверяем наличие данных
                if(($schedule_mass[$n]['worktime_begin'] != null) && ($schedule_mass[$n]['worktime_end'] != null)){

                    $worktime_begin = timeToSec($schedule_mass[$n]['worktime_begin']);
                    $worktime_end = timeToSec($schedule_mass[$n]['worktime_end']);

                    // Делаем корректировку данных - если время работы переходит на следующие сутки (Например, с 09:00 до 03:00)
                    if($worktime_end < $worktime_begin){
                       $worktime_end = (86400 - $worktime_begin) + $worktime_end;
                    } else {$worktime_end = $worktime_end - $worktime_begin;};

                    $mass_time[] = [
                        'schedule_id' => $schedule_id,
                        'weekday' => $n + 1,
                        'worktime_begin' => $worktime_begin,
                        'worktime_interval' => $worktime_end
                    ];
                };    
            };

            // Отдаем готовый массив
            return $mass_time;
        };

?>