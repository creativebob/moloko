<?php

use Carbon\Carbon;
use App\LegalForm;

use App\FirstName;
use App\Surname;

function num_format($number, $value) {

    $result = number_format($number, $value, ',', ' ');
    return $result;
}

function num_clean($number, $value) {

    $result = number_format($number, $value, '.', '');
    return $result;
}

function decor_access_block($access_block) {
    if($access_block == 1){$result = "Блокирован";} else {$result = "Открыт";};

    return $result;
}

function decor_user_type($user_type) {
    if($user_type == 1){$result = "Свой";}
    elseif($user_type == 0){$result = "Чужой";}
    else {$result = "Статус не определен";};

    return $result;
}

function cleanPhone($str) {
    $ptn = "/[^0-9]/";
    $rpltxt = "";
    $result = preg_replace($ptn, $rpltxt, $str);

    return $result;
}

function secToTime($sec) {
    $hour = floor($sec/3600);
    $sec = $sec - ($hour*3600);
    $min = floor($sec/60);
    $sec = $sec - ($min*60);
    if($hour < 10){$hour = '0' . $hour;}
    if($min < 10){$min = '0' . $min;}

    return $hour.":".$min;
}

function timeToSec($time) {
    $mass = explode(":", $time);
    $hour = $mass[0];
    $min = $mass[1];
    $sec = $hour*60*60 + $min*60;

    return $sec;
}

// ====================================================================
// Преобразует данные (Полученную на шаблоне дату)
// из плагина PickMeUp в формат базы данных
// ====================================================================

function outPickMeUp($date) {

    $date_for_mysql = Carbon::createFromFormat('d.m.Y', $date)->format('Y-m-d');
    return $date_for_mysql;
}



// ====================================================================
// Преобразует дату (Формат: date) для вывода
// на шаблон через плагин PickMeUp
// Если date равна Null, то отдает текущую дату
// ====================================================================

function inPickMeUp($date) {

    if(empty($date)){
        $date = Carbon::today()->format('d.m.Y');
    } else {
        $date = $date->format('d.m.Y');
    };

    return $date;
}



// Принимает 11 значное число: номер телефона Возвращает строку: Удобочитаемый номер телефона со скобочками и дефисом
function decorPhone($value) {

    if (strlen($value) == 11 ) {
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
        }
    }

    if (strlen($value) < 6) {
        $result = "Номер не указан";
    }

    if (strlen($value) == 0) {
        $result = NULL;
    }

    return $result;
}

function callPhone($value) {

    // dd($value);

    if(strlen($value) == 11 ){
        // $value = strtolower($value, "UTF-8");
        $rest1 = '+7';
        $rest2 = mb_substr($value, 1, 10); // возвращает "bcd"
        $result = $rest1.$rest2;
    };

    if(strlen($value) < 6){
        $result = "Номер не указан";
    };

    if(strlen($value) == 0){
        $result = NULL;
    };

    return $result;
}

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
    if(($method == 'edit')||($method == 'update')||($method == 'myprofile')){ return 'update';};
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


// Скрываем бога и ставим Id робота
function hideGod($user){

    // Если бог, то ставим автором робота
    if (is_null($user)) {
        $user_id = 1;
    } else {
        $user_id = $user->god == 1 ? 1 : $user->id;
    }

    return $user_id;
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
    for ($n = 0; $n < 7; $n++) {

        // Проверяем наличие данных
        if (($schedule_mass[$n]['worktime_begin'] != null) && ($schedule_mass[$n]['worktime_end'] != null)) {
         $worktime_begin = timeToSec($schedule_mass[$n]['worktime_begin']);
         $worktime_end = timeToSec($schedule_mass[$n]['worktime_end']);

            // Делаем корректировку данных - если время работы переходит на следующие сутки (Например, с 09:00 до 03:00)
         if ($worktime_end < $worktime_begin) {
            $worktime_end = (86400 - $worktime_begin) + $worktime_end;
        } else {$worktime_end = $worktime_end - $worktime_begin;};

        $mass_time[] = [
            'schedule_id' => $schedule_id,
            'weekday' => $n + 1,
            'worktime_begin' => $worktime_begin,
            'worktime_interval' => $worktime_end
        ];
    }
}

// Отдаем готовый массив
return $mass_time;
}

// -----------------------------------------------------------------------------------------------
// ПАРСИМ ДАТУ И ОТДАЕМ ДЕНЬ НЕДЕЛИ В НУЖНОМ ФОРМАТЕ ---------------------------------------------
// -----------------------------------------------------------------------------------------------

function getWeekDay($date, $type = 0){

// Получаем дату и необходимый формат вывоода
    $date = Carbon::parse($date);
    $weekday = $date->dayOfWeek;

    if($type == 0){

        switch ($weekday) {
            case 0: $weekday = 'Воскресенье'; break;
            case 1: $weekday = 'Понедельник'; break;
            case 2: $weekday = 'Вторник'; break;
            case 3: $weekday = 'Среда'; break;
            case 4: $weekday = 'Четверг'; break;
            case 5: $weekday = 'Пятница'; break;
            case 6: $weekday = 'Суббота'; break;
        }

    } else {

        switch ($weekday) {
            case 0: $weekday = 'Вс'; break;
            case 1: $weekday = 'Пн'; break;
            case 2: $weekday = 'Вт'; break;
            case 3: $weekday = 'Ср'; break;
            case 4: $weekday = 'Чт'; break;
            case 5: $weekday = 'Пт'; break;
            case 6: $weekday = 'Сб'; break;
        }

    }

    // Отдаем день недели
    return $weekday;
}

function getMonth($date, $type = 0){

// Получаем дату и необходимый формат вывоода
    $date = Carbon::parse($date);
    $month = $date->month;

    if($type == 0){

        switch ($month) {
            case 1: $month = 'Январь'; break;
            case 2: $month = 'Февраль'; break;
            case 3: $month = 'Март'; break;
            case 4: $month = 'Апрель'; break;
            case 5: $month = 'Май'; break;
            case 6: $month = 'Июнь'; break;
            case 7: $month = 'Июль'; break;
            case 8: $month = 'Август'; break;
            case 9: $month = 'Сентябрь'; break;
            case 10: $month = 'Октябрь'; break;
            case 11: $month = 'Ноябрь'; break;
            case 12: $month = 'Декабрь'; break;
        }

    } else {

        switch ($month) {
            case 1: $month = 'Янв'; break;
            case 2: $month = 'Фев'; break;
            case 3: $month = 'Мар'; break;
            case 4: $month = 'Апр'; break;
            case 5: $month = 'Май'; break;
            case 6: $month = 'Июн'; break;
            case 7: $month = 'Июл'; break;
            case 8: $month = 'Авг'; break;
            case 9: $month = 'Сен'; break;
            case 10: $month = 'Окт'; break;
            case 11: $month = 'Ноя'; break;
            case 12: $month = 'Дек'; break;
        }

    }

    // Отдаем день недели
    return $month;
}

// Делаем заглавной первую букву
function get_first_letter($name){

    $first = mb_substr($name,0,1, 'UTF-8'); //первая буква
    $last = mb_substr($name,1); //все кроме первой буквы
    $first = mb_strtoupper($first, 'UTF-8');
    $last = mb_strtolower($last, 'UTF-8');
    $name = $first.$last;

    // Отдаем имя
    return $name;
}




// ==============================================================================================
// ФУНКЦИЯ - ФОРМАТИРОВАНИЕ РЕЖИМА РАБОТЫ ДЛЯ ФОРМЫ
// ==============================================================================================


// Получаем модель из тега
function worktime_to_format($worktime_mass){

    for($x = 1; $x<8; $x++){

        if(isset($worktime_mass[$x]->worktime_begin)){

            $worktime_begin = $worktime_mass[$x]->worktime_begin;
            $str_worktime_begin = secToTime($worktime_begin);
            $worktime[$x]['begin'] = $str_worktime_begin;

        } else {

            $worktime[$x]['begin'] = null;
        };

        if(isset($worktime_mass[$x]->worktime_interval)){

            $worktime_interval = $worktime_mass[$x]->worktime_interval;

            if(($worktime_begin + $worktime_interval) > 86400){

                $str_worktime_interval = secToTime($worktime_begin + $worktime_interval - 86400);
            } else {

                $str_worktime_interval = secToTime($worktime_begin + $worktime_interval);
            };

            $worktime[$x]['end'] = $str_worktime_interval;
        } else {

            $worktime[$x]['end'] = null;
        }

    };

    return $worktime;
};



// ==============================================================================================
// ФУНКЦИИ - ИНТЕРЕСОВ ЛИДА (CHOICE)
// ==============================================================================================


// Получаем модель из тега
function getChoiceFromTag($choice_tag){

    $exp_result = explode('-', $choice_tag);
    $modelAndId['type'] = null;
    $modelAndId['id'] = null;

    switch ($exp_result[0]) {
        case 'goods': $modelAndId['type'] = 'App\GoodsCategory'; break;
        case 'service': $modelAndId['type'] = 'App\ServicesCategory'; break;
        case 'raw': $modelAndId['type'] = 'App\RawsCategory'; break;
    }

    if(isset($exp_result[1])){$modelAndId['id'] = $exp_result[1];};
    return $modelAndId;
}


// Генерируем значение value для списка интересов (choice)
function genChoiceTag($item){

    $choice_tag = '';
    if($item->choice_type == 'App\GoodsCategory'){$choice_tag = "goods-" . $item->choice_id;};
    if($item->choice_type == 'App\ServicesCategory'){$choice_tag = "service-" . $item->choice_id;};
    if($item->choice_type == 'App\RawsCategory'){$choice_tag = "raw-" . $item->choice_id;};

    // Отдаем
    return $choice_tag;
}



// Чистит имена компаний от организационных форм, и отдает id такой формы (legal_form_id)
// если форма присутствовала в имени
function cleanNameLegalForm($string){

    $legal_form_list = LegalForm::get();
    $legal_form_name = $legal_form_list->pluck('name', 'id');
    $legal_form_full_name = $legal_form_list->pluck('full_name', 'id');

    // dd($legal_form_full_name);
    $result = false;

    foreach($legal_form_name as $key => $value){

        if (preg_match("/(^|\s)" . $value . "\s/i", $string, $matches)) {
            $result['name'] = str_replace($matches[0], "", $string);
            $result['legal_form_id'] = $key;
            return $result;
        };

    }

    return false;

}

// Получаем график работы у юзера или бога
function getSchedule($user){

    if ($user->god == 1) {
        $schedule = $user->company->filials->first()->main_schedule ?? $user->company->main_schedule;
    } else {

        $schedule = $user->staff->first()->main_schedule ?? $user->staff->first()->filial->main_schedule ?? $user->staff->first()->company->main_schedule;
    }

    // Если бог, то ставим автором робота
    return $schedule;
}

// Отдает дату и время дедлайна в зависимости от графика работы
// Получаем график работы чего либо, и количество секунд, через которые нужно назначить
function getDeadline ($schedule = null, $seconds) {

    $worktime = null;

    // Если график существует
    if ($schedule) {
        $schedule = $schedule->load('worktimes');

        // Порядковый номер дня
        $day = date('N');
        // $day = 7;
        $days_count = 0;

        for ($i = 1; $i < 9; $i++) {

            // Ищем в графике текущий рабочий день, или ближайший следующий, в цикле из 7ми раз
            $worktime = $schedule->worktimes->where('weekday', $day)->first();
            // dd($worktime);

            // Если находим расписание на день
            if ($worktime) {

                // Если рабочий день сегодня, сверяем время до конца дня
                if ($worktime->weekday == date('N')) {

                    $worktime_end = Carbon::createFromTimestamp(today()->timestamp + $worktime->worktime_begin + $worktime->worktime_interval);
                    $deadline = Carbon::createFromTimestamp(now()->timestamp + $seconds);

                    if ($deadline <= $worktime_end) {
                        // dd('Дедлайн на текущий день - '.$deadline);
                        return $deadline;
                        break;
                    }
                } else {
                    // Дата и время начала рабочего дня
                    $deadline = Carbon::createFromTimestamp(today()->timestamp + $worktime->worktime_begin + $seconds)->addDays($days_count);
                    // dd('Дедлайн на ближайший день - '.$deadline);
                    return $deadline;
                    break;
                }
            }

            $day++;
            $day = $day > 7 ? 1 : $day;
            $days_count++;

            // echo "Итерация = ".$i.", день = ".$day.", график - ". (isset($worktime) ? $worktime->id : "null")."<br>";
        }
        // dd($days_count);
    }

    // Если нет графика, или в графике нет рабочих дней отдаем текущее время + пришедшие секунды
    if (!$schedule || !$worktime) {
        return Carbon::createFromTimestamp(now()->timestamp + $seconds);
    }
}



// Функция которая принимает строчку текста, а отдает массив с именем, фамилией, отчеством и полом
function getNameUser($string) {

            $crop_name = explode(' ', $string);
            Log::info('Пробуем разбить пришедшее имя на части');

            // Подготовительный этап

            $first_names = FirstName::get();
            $surnames = Surname::get();


            $result['gender'] = 1;

            $count = count($crop_name);
            Log::info('Кол-во элементов: ' . $count);

            // Формируем массив
            $parts = [];
            foreach($crop_name as $key => $item){

                $parts[$key]['value'] = Str::title($item);
                Log::info($parts[$key]['value']);

                $parts[$key]['opinion'] = false;
                $parts[$key]['gender'] = false;

                // Даем нулевые вероятности по каждому предположению
                $parts[$key]['odds_first_name'] = 0;
                $parts[$key]['odds_second_name'] = 0;
                $parts[$key]['odds_patronymic'] = 0;

            }

            // dd($parts);

            // ---------- СТАТИСТИЧЕСКИЙ МЕТОД ПРЕДПОЛОЖЕНИЯ

            Log::info('СТАТИСТИЧЕСКИЙ МЕТОД!');
            if($count == 1){

                // Система предполагает, что это имя
                $parts[0]['opinion'] = 'first_name';
                $parts[0]['odds_first_name'] = $parts[0]['odds_first_name'] + 1;
            };

            if($count == 2){

                // Система предполагает, что это имя и фамилия
                $parts[0]['opinion'] = 'first_name';
                $parts[0]['odds_first_name'] = $parts[0]['odds_first_name'] + 1;

                $parts[1]['opinion'] = 'second_name';
                $parts[1]['odds_second_name'] = $parts[0]['odds_second_name'] + 1;

            };

            if($count == 3){

                // Система предполагает, что это фамилия, имя и отчество
                $parts[0]['opinion'] = 'second_name';
                $parts[0]['odds_second_name'] = $parts[0]['odds_second_name'] + 1;

                $parts[1]['opinion'] = 'first_name';
                $parts[1]['odds_first_name'] = $parts[1]['odds_first_name'] + 1;

                $parts[2]['opinion'] = 'patronymic';
                $parts[2]['odds_patronymic'] = $parts[2]['odds_patronymic'] + 1;
            };

            // dd($parts);


            // ---------- СИНТАКСИЧЕСКИЙ МЕТОД ПРЕДПОЛОЖЕНИЯ

            // Система анализирует окончание слова и создает новые синтаксические предположения

            for($x = 0; $count > $x; $x = $x + 1){

                Log::info('СИНТАКСИЧЕСКИЙ МЕТОД:');

                if(Str::endsWith($parts[$x]['value'], array('ов', 'ко', 'ин', 'ский', 'чин', 'цкий', 'чев', 'чёв', 'цев'))){
                    $parts[$x]['opinion'] = 'second_name';
                    $parts[$x]['gender'] = 1;

                    $parts[$x]['odds_second_name'] = $parts[$x]['odds_second_name'] + 10;
                    Log::info('Обнаружены признаки мужской фамилии в ' . $parts[$x]['value']);
                }

                if(Str::endsWith($parts[$x]['value'], array('ова', 'ина', 'ская', 'чина', 'цкая', 'чёва', 'цева'))){
                    $parts[$x]['opinion'] = 'second_name';
                    $parts[$x]['gender'] = 0;

                    $parts[$x]['odds_second_name'] = $parts[$x]['odds_second_name'] + 10;
                    Log::info('Обнаружены признаки женской фамилии в ' . $parts[$x]['value']);
                }


                if(Str::endsWith($parts[$x]['value'], array('вич'))){
                    $parts[$x]['opinion'] = 'patronymic';
                    $parts[$x]['gender'] = 1;

                    $parts[$x]['odds_patronymic'] = $parts[$x]['odds_patronymic'] + 10;
                    Log::info('Обнаружены признаки мужского отчества в ' . $parts[$x]['value']);
                }

                if(Str::endsWith($parts[$x]['value'], array('вна'))){
                    $parts[$x]['opinion'] = 'patronymic';
                    $parts[$x]['gender'] = 0;

                    $parts[$x]['odds_patronymic'] = $parts[$x]['odds_patronymic'] + 10;
                    Log::info('Обнаружены признаки женского отчества в ' . $parts[$x]['value']);
                }



                // ---------- ТОЧНЫЙ МЕТОД ПРЕДПОЛОЖЕНИЯ

                // Система смотрит в базу имен
                Log::info('ТОЧНЫЙ МЕТОД:');

                Log::info('Смотрим в базу имен');
                $first_name_base = $first_names->where('name', $parts[$x]['value']);
                if($first_name_base->count() == 1){

                    $parts[$x]['base'] = true;
                    $parts[$x]['opinion'] = 'first_name';
                    $parts[$x]['gender'] = $first_name_base->first()->gender;

                    // $result['first_name'] = $parts[$x]['value'];

                    $parts[$x]['odds_first_name'] = $parts[$x]['odds_first_name'] + 50;
                    Log::info('Имя найдено!');

                } else {
                    Log::info('Имя НЕ найдено!');
                };


                Log::info('Смотрим в базу мужских отчеств');
                $patronymic_male_base = $first_names->where('patronymic_male', $parts[$x]['value']);
                if($patronymic_male_base->count() == 1){

                    $parts[$x]['base'] = true;
                    $parts[$x]['opinion'] = 'patronymic';
                    $parts[$x]['gender'] = $patronymic_male_base->first()->gender;

                    // $result['gender'] = $patronymic_male_base->first()->gender;
                    // $result['patronymic'] = $parts[$x]['value'];

                    $parts[$x]['odds_patronymic'] = $parts[$x]['odds_patronymic'] + 50;
                    Log::info('Мужское отчество найдено!');

                };

                Log::info('Смотрим в базу женских отчеств');
                $patronymic_female_base = $first_names->where('patronymic_female', $parts[$x]['value']);
                if($patronymic_female_base->count() == 1){

                    $parts[$x]['base'] = true;
                    $parts[$x]['opinion'] = 'patronymic';
                    $parts[$x]['gender'] = $patronymic_female_base->first()->gender;

                    // $result['gender'] = $patronymic_female_base->first()->gender;
                    // $result['patronymic'] = $parts[$x]['value'];

                    $parts[$x]['odds_patronymic'] = $parts[$x]['odds_patronymic'] + 50;
                    Log::info('Женское отчество найдено!');

                };



                    // Система продолжает поиск в базе фамилий
                    // Смотрим в мужских фамилиях
                    $surname_male_base = $surnames->where('surname_male', $parts[$x]['value']);
                    Log::info('Результат поиска "' . $parts[$x]['value'] . '" в male: ' . $surname_male_base);

                    if($surname_male_base->count() == 1){

                        $parts[$x]['base'] = true;
                        $parts[$x]['opinion'] = 'second_name';
                        $parts[$x]['gender'] = 1;

                        // $result['second_name'] = $parts[$x]['value'];
                        // $result['gender'] = 1;

                        $parts[$x]['odds_second_name'] = $parts[$x]['odds_second_name'] + 50;
                        Log::info('Найдена мужская фамилия!');

                    } else {
                        Log::info('Фамилия в мужской базе фамилий не найдена!');
                    }

                    // Смотрим в женских фамилиях
                    $surname_female_base = $surnames->where('surname_female', $parts[$x]['value']);
                    Log::info('Результат поиска "' . $parts[$x]['value'] . '" в female: ' . $surname_female_base);

                    if($surname_female_base->count() == 1){

                        $parts[$x]['base'] = true;
                        $parts[$x]['opinion'] = 'second_name';
                        $parts[$x]['gender'] = 0;

                        // $result['second_name'] = $parts[$x]['value'];
                        // $result['gender'] = 0;

                        $parts[$x]['odds_second_name'] = $parts[$x]['odds_second_name'] + 50;
                        Log::info('Найдена женская фамилия!');

                    } else {
                        Log::info('Фамилия в женской базе фамилий не найдена!');
                    }

                    Log::info($parts);


            } // Конец for

            // Формируем массив по вероятностям
            $odds = [];

            // Ищем имя

            $first_name_max = 0;
            $second_name_max = 0;
            $patronymic_max = 0;

            for($x = 0; $count > $x; $x = $x + 1){

                if($parts[$x]['odds_first_name'] > $first_name_max){
                    $first_name_max = $parts[$x]['odds_first_name'];
                    $result['first_name'] = $parts[$x]['value'];
                    $result['gender'] = $parts[$x]['gender'];
                }

                if($parts[$x]['odds_second_name'] > $second_name_max){
                    $second_name_max = $parts[$x]['odds_second_name'];
                    $result['second_name'] = $parts[$x]['value'];
                }

                if($parts[$x]['odds_patronymic'] > $patronymic_max){
                    $patronymic_max = $parts[$x]['odds_patronymic'];
                    $result['patronymic'] = $parts[$x]['value'];
                }

            }


            // Заполнение пустых переменных
            if(!isset($result['first_name'])){$result['first_name'] = 'Имя не известно';}
            if(!isset($result['second_name'])){$result['second_name'] = null;}
            if(!isset($result['patronymic'])){$result['patronymic'] = null;}
            if(!isset($result['gender'])){$result['gender'] = 1;} // По умолчанию ставим мужской пол

            // dd($result);

            // Подводим итоги:
            // for($x = 0; $count > $x; $x = $x + 1){

            //     if((!isset($result['first_name']))&&(!isset($parts[$x]['base']))){

            //         $result['first_name'] = $odds['first_name'];
            //         $result['gender'] = $parts[$x]['gender'];
            //     }

            //     if((!isset($result['second_name']))&&(!isset($parts[$x]['base']))){
                    
            //         $result['second_name'] = $odds['second_name'];
            //         $result['gender'] = $parts[$x]['gender'];
            //     }

            //     if((!isset($result['patronymic']))&&(!isset($parts[$x]['base']))){
                    
            //         $result['patronymic'] = $odds['patronymic'];
            //         $result['gender'] = $parts[$x]['gender']; 
            //     }
            // }

            return $result;

}




?>
