<?php
        use App\Scopes\ModerationScope;
        use App\Department;

        function operator_right($entity_name, $entity_dependence, $method) {

        // Получаем сессию
        $session  = session('access');
        if(!isset($session)){abort(403, 'Нет сессии!');};

        if($session['user_info']['user_status'] == null){


            // ПРОВЕРЯЕМ ПРАВО НА ПРОСМОТР ВСЕХ ЗАПИСЕЙ КОМПАНИИ (ВСЕ ФИЛИАЛЫ)  -----------------------------------------------------------------------------------
            // Если выбрано "Нет ограичений" мы снимаем филиальную зависимость

            $mass_right = getRight('nolimit', $entity_name, $session);
            // dd($mass_right);

            $nolimit = $mass_right['result'];

            if($nolimit == true){

                // Видим все записи компании 
                $dependence = false;

            } else {

                // Видим только записи филиалов, которые нам предоставлены ролями
                if($entity_dependence == true) {$dependence = true;} else {$dependence = false;};

            };


            // ВКЛЮЧЕНИЕ ИЛИ ОТКЛЮЧЕНИЕ РАСШИРЯЮЩЕГО СПИСКА АВТОРОВ ------------------------------------------------------------------------------------

            // Подключать ли пользователю список авторов?
            $use_authors = true; // Да

            // ПРОВЕРЯЕМ ПРАВО НА ПРОСМОТР ЗАПИСЕЙ ДРУГИХ АВТОРОВ  ----------------------------------------------------------------------------------------
            // Проверяем в правах (которые записаны в сессию) наличие права на просмотр чужих записей 
            // и отсутствие такого запрета
            
            $mass_right = getRight('authors', $entity_name, $session);
            $authors_status = $mass_right['result'];
            // dd($mass_right);

            if($mass_right['result'] == true){

                // Разрешено ли использовать список авторов
                if($use_authors){

                    // Если РАЗРЕШЕНО использовать список авторов
                    // Проверяем - есть ли в сессии авторы?
                    if(isset($session['all_rights']['authors-'.$entity_name.'-allow']['authors'])) 
                    {

                        // Берем их так как они есть
                        $list_authors = $session['all_rights']['authors-'.$entity_name.'-allow']['authors'];

                    } else {

                        // Пишем null так как авторы не нашлись
                        $list_authors = null;
                        // abort(403, 'Не найден список авторов для пользователя!');

                    };

                } else {

                    // Если не разрешено использовать список авторов, то обнуляем список который пришел из сессии
                    $list_authors = null;

                };
            };

            if($mass_right['result'] == false){

                // Если НЕ находим право на чтение чужих авторов - пишем статус false в $authors_status
                $list_authors = null;
                $authors_status = null;

            };


        } else {

            // ЕСЛИ МЫ БОГИ

            $dependence = false;
            $moderator = true;
            $system_item = true;
            $filials = null;
            $departments = null;
            $authors_status = true;
            $list_authors = null;
            $automoderate = true;

        };

        // ФОРМИРУЕМ РЕЗУЛЬТАТЫ И ОФОРМЛЯЕМ ИХ В ВИДЕ МАССИВА ДЛЯ ОТПРАВКИ В КОНТРОЛЛЕР

        $mass_right = getRight($method, $entity_name, $session);

        $answer['result'] = $mass_right['result'];

        $answer['entity_name'] = $entity_name;
        $answer['dependence'] = $dependence;
        $answer['system_item'] = getRight('system', $entity_name, $session);
        $answer['moderator'] = getRight('moderator', $entity_name, $session);
        $answer['automoderate'] = getRight('automoderate', $entity_name, $session);
        $answer['filials'] = $mass_right['filials'];
        $answer['departments'] = $mass_right['departments'];
        $answer['authors_status'] = $authors_status;
        $answer['list_authors'] = $list_authors;
        $answer['user_id'] = $session['user_info']['user_id'];
        $answer['user_status'] = $session['user_info']['user_status'];
        $answer['company_id'] = $session['user_info']['company_id'];

        $answer['session'] = $session;

        $answer['all_authors']['authors_status'] = $authors_status;
        $answer['all_authors']['list_authors'] = $list_authors;
        $answer['all_authors']['user_id'] = $session['user_info']['user_id'];

        // dd($answer);

        return $answer;
    }


    // Функция которая получает список разрешенных правами департаментов на указанное право
    function getLS($entity_name, $method, $type_list) {

            // Получаем сессию
            $session  = session('access');
            if(!isset($session)){abort(403, 'Нет сессии!');};

            if(isset($session['all_rights'][$method . '-'. $entity_name .'-allow'][$type_list])) {



                if((isset($session['all_rights']['nolimit-' . $entity_name . '-allow']))&&(!isset($session['all_rights']['nolimit-' . $entity_name . '-deny']))) {

                    // Получаем список всех отделов компании
                    $departments = $session['company_info']['filials'];


                } else {

                    // Получаем список доступных отделов
                    $departments = $session['all_rights'][$method . '-'. $entity_name .'-allow'][$type_list];
                    // dd($departments);
                };



                // Нет ли блокировки этого права?
                if(isset($session['all_rights'][$method . '-'. $entity_name .'-deny'][$type_list])) {

                    $departments = collect($departments);

                    // Блокировка найдена
                    $minus_departments = collect($session['all_rights'][$method . '-'. $entity_name .'-deny'][$type_list])->keys();

                    // Вычетаем из списка департаментов - департаменты для которых есть запрет
                    $departments = $departments->except($minus_departments);
                };                
            };


        if($session['user_info']['user_status'] == 1){

            if($session['user_info']['company_id'] == null){

                $departments = null;
            } else {

                // Получаем список отделов для бога
                // dd($session['company_info'][$type_list]);
                if(!isset($session['company_info'][$type_list])){abort(403, 'Необходимо создать филиал');};
                $departments = $session['company_info'][$type_list];
            };
        };

        if(!isset($departments)){$departments = null;};
        return $departments;
    }


    //-----------------------------------------------------------------------------------------------------------------------------------------------------------
    // ФУНКЦИЯ ПОЛУЧЕНИЯ ПРАВА ФИЛИАЛО НЕ ЗАВИСИМЫХ СУЩНОСТЕЙ ---------------------------------------------------------------------------------------------------
    //-----------------------------------------------------------------------------------------------------------------------------------------------------------

    function getRight($method, $entity_name, $session){

                // Получаем список филиалов в которых разрешено
                if(isset($session['all_rights'][$method . '-'. $entity_name .'-allow'])) {

                    if(isset($session['all_rights'][$method . '-'. $entity_name .'-allow']['filials'])) {
                        $mass_filials_allow = $session['all_rights'][$method . '-'. $entity_name .'-allow']['filials'];
                    } else {$mass_filials_allow = null;};
                } else {$mass_filials_allow = null;};


                // Получаем список филиалов в которых запрещено
                if(isset($session['all_rights'][$method . '-'. $entity_name .'-deny'])) {

                    if(isset($session['all_rights'][$method . '-'. $entity_name .'-deny']['filials'])) {
                        $mass_filials_deny = $session['all_rights'][$method . '-'. $entity_name .'-deny']['filials'];
                    } else {$mass_filials_deny = null;};
                } else {$mass_filials_deny = null;};


                $filials_diff = collect($mass_filials_allow)->diff($mass_filials_deny)->keys()->toArray();
                $filials = $filials_diff;


                // Получаем список филиалов в которых разрешено
                if(isset($session['all_rights'][$method . '-'. $entity_name .'-allow'])) {

                    if(isset($session['all_rights'][$method . '-'. $entity_name .'-allow']['departments'])) {
                        $mass_departments_allow = $session['all_rights'][$method . '-'. $entity_name .'-allow']['departments'];
                    } else {$mass_departments_allow = null;};
                } else {$mass_departments_allow = null;};


                // Получаем список филиалов в которых запрещено
                if(isset($session['all_rights'][$method . '-'. $entity_name .'-deny'])) {

                    if(isset($session['all_rights'][$method . '-'. $entity_name .'-deny']['departments'])) {
                        $mass_departments_deny = $session['all_rights'][$method . '-'. $entity_name .'-deny']['departments'];
                    } else {$mass_departments_deny = null;};
                } else {$mass_departments_deny = null;};


                $departments_diff = collect($mass_departments_allow)->diff($mass_departments_deny)->keys()->toArray();
                $departments = $departments_diff;


                if(count($departments) > 0){$result = true;} else {$result = false;};

                // if(($method == 'moderator')&&(count($departments)>0)){$moderator = true;} else {$moderator = false;};
                // if(($method == 'automoderate')&&(count($departments)>0)){$automoderate = true;} else {$automoderate = false;};


                $mass_right['result'] = $result;
                $mass_right['departments'] = $departments;
                $mass_right['filials'] = $filials;

                return $mass_right;


            };


        // Проверка наличия дополнительного (функционального) права
        function extra_right($alias) {

            // Получаем сессию
            $session  = session('access');
            if(!isset($session)){abort(403, 'Нет сессии!');};

            if(empty($session['user_info']['extra_rights'])){
                $result = false;
            } else {
                $result = $session['user_info']['extra_rights']->where('alias', $alias)->isNotEmpty();
            }
            return $result;
        }


?>