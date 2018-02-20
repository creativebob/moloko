<?php
        use App\Scopes\ModerationScope;
        use App\Department;

        function operator_right($entity_name, $entity_dependence, $method) {

        // Получаем сессию
        $session  = session('access');
        if(!isset($session)){abort(403, 'Нет сессии!');};

        if($session['user_info']['user_status'] == null){


            // Получаем список авторов
            // $list_authors = $session['list_authors'];


            // ПРОВЕРЯЕМ ПРАВО НА ПРОСМОТР НЕ ОТМОДЕРИРОВАННЫХ ЗАПИСЕЙ  ------------------------------------------------------------------------------------------
            // Проверяем право просмотра системных записей:
            
            if(isset($session['all_rights']['moderator-'.$entity_name.'-allow']) && (!isset($session['all_rights']['moderator-'.$entity_name.'-deny'])))
            {

                // Буду иметь возможность модерировать записи - буду их видеть
                $moderator = ModerationScope::class;
                // dd($moderator);

            } else {

                // Не буду видеть не отмодерированные записи и не смогу их модерировать
                $moderator = null;

            };



            // ПРОВЕРЯЕМ ПРАВО НА ПОЛНОЦЕННОЕ СОЗДАНИЕ ЗАПИСИ (Без необходимости последующего модерирования) -----------------------------------------------------
            // Проверяем право просмотра системных записей:
            
            if(isset($session['all_rights']['automoderate-'.$entity_name.'-allow']) && (!isset($session['all_rights']['automoderate-'.$entity_name.'-deny'])))
            {

                // Пишем сразу
                $automoderate = true;
            } else {

                // Пишем ущербную запись (требуеться модерация)
                $automoderate = false;
            };



            // ПРОВЕРЯЕМ ПРАВО НА ПРОСМОТР ВСЕХ ЗАПИСЕЙ КОМПАНИИ (ВСЕ ФИЛИАЛЫ)  -----------------------------------------------------------------------------------
            // Если выбрано "Нет ограичений" мы снимаем филиальную зависимость

            if(isset($session['all_rights']['nolimit-'.$entity_name.'-allow']) && (!isset($session['all_rights']['nolimit-'.$entity_name.'-deny'])))
            {

                // Видем все записи компании 
                $dependence = false;
            } else {

                // Видем только записи филиалов, которые нам предоставлены ролями
                if($entity_dependence == true) {$dependence = true;} else {$dependence = false;};
            };



            // ПРОВЕРЯЕМ ПРАВО НА ПРОСМОТР ОБЩЕГО СПИСКА  --------------------------------------------------------------------------------------------------------
            // Проверяем в правах (которые записаны в сессию) наличие права на просмотр общего списка пользователей 
            // и отсутствие запрета. Если есть зависимость от филиала - получаем списки с ID.
            
            if(isset($session['all_rights']['index-'.$entity_name.'-allow'])&&(!isset($session['all_rights']['index-'.$entity_name.'-deny'])))
            {

                // Получаем список ID филиалов в которых присутствует право на просмотр списка пользователей
                $filials = collect($session['all_rights']['index-'.$entity_name.'-allow']['departments'])->keys()->toarray();
                if(!isset($filials)){$filials = null;};


                // Получаем список ID департаментов в которых присутствует право на просмотр списка пользователей
                $departments = collect($session['all_rights']['index-'.$entity_name.'-allow']['filials'])->keys()->toarray();
                if(!isset($departments)){$departments = null;};

            } else {

                if(!isset($filials)){$filials = null;};
                if(!isset($departments)){$departments = null;};
                abort(403, "Нет прав на операцию! Отсутствуют списки ID");

            };


            // ЗАВИСИМОСТЬ ОТ СИСТЕМНЫХ ЗАПИСЕЙ  -----------------------------------------------------------------------------------------------------------
            // Проверяем право просмотра системных записей:
            
            if(isset($session['all_rights']['system-'.$entity_name.'-allow']) && (!isset($session['all_rights']['system-'.$entity_name.'-deny'])))
            {

                // Будем видеть системные записи
                $system_item = 1;
            } else {

                // Не будем видеть системные записи
                $system_item = null;
            };




            // ВКЛЮЧЕНИЕ ИЛИ ОТКЛЮЧЕНИЕ РАСШИРЯЮЩЕГО СПИСКА АВТОРОВ ------------------------------------------------------------------------------------

            // Подключать ли пользователю список авторов?
            $use_authors = true; // Да


            // ПРОВЕРЯЕМ ПРАВО НА ПРОСМОТР ЗАПИСЕЙ ДРУГИХ АВТОРОВ  ----------------------------------------------------------------------------------------
            // Проверяем в правах (которые записаны в сессию) наличие права на просмотр чужих записей 
            // и отсутствие такого запрета
            if(isset($session['all_rights']['authors-'.$entity_name.'-allow']) && (!isset($session['all_rights']['authors-'.$entity_name.'-deny'])))
            {

                // Если находим право на чтение чужих авторов - пишем статус true в $authors_status
                $authors_status = true;

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

            } else {

                // Если НЕ находим право на чтение чужих авторов - пишем статус false в $authors_status
                $list_authors = null;
                $authors_status = null;
            };

        } else {

            // ЕСЛИ МЫ БОГИ

            $dependence = false;
            $moderator = ModerationScope::class;
            $system_item = 1;
            $filials = null;
            $departments = null;
            $authors_status = true;
            $list_authors = null;
            $automoderate = true;

        };

        // ФОРМИРУЕМ РЕЗУЛЬТАТЫ И ОФОРМЛЯЕМ ИХ В ВИДЕ МАССИВА ДЛЯ ОТПРАВКИ В КОНТРОЛЛЕР


        $answer['entity_name'] = $entity_name;
        $answer['dependence'] = $dependence;
        $answer['moderator'] = $moderator;
        $answer['system_item'] = $system_item;
        $answer['filials'] = $filials;
        $answer['departments'] = $departments;
        $answer['authors_status'] = $authors_status;
        $answer['list_authors'] = $list_authors;
        $answer['automoderate'] = $automoderate;
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

    function getListsDepartments($company_id) {

        // ПОДГОТОВКА СПИСКОВ ФИЛИАЛОВ И ОТДЕЛОВ КОМПАНИИ ДЛЯ SELECT ----------------------------------------------------------------------------
        // Получаем сессию
        
        $session  = session('access');
        if(!isset($session)){abort(403, 'Нет сессии!');};

        // Если бог, то будем выбирать списки филиалов и отделов
        if($session['user_info']['user_status'] == 1){

            if($session['user_info']['company_id'] == null){abort(403, "Необходимо авторизоваться под компанией!");};
            $departments = Department::whereCompany_id($company_id)->get();

            foreach($departments as $department){
                $list_departments[$department->id] = $department->department_name;
                if($department->filial_status == 1){$list_filials[$department->id] = $department->department_name;};
            };

        } elseif($session['user_info']['user_status'] == null) {

            //Если обычный пользователь, то смотрим списки в сессии
            if(isset($session['all_rights']['update-users-allow']['filials'])){
                $list_filials = $session['all_rights']['update-users-allow']['filials'];
            } else {abort(403, 'Вы похоже не трудоустроены!');};

            if(isset($session['all_rights']['update-users-allow']['departments'])){
                $list_departments = $session['all_rights']['update-users-allow']['departments'];
            } else {abort(403, 'Вы похоже не трудоустроены!');};

        };

        $lists_departments['list_filials'] = $list_filials;
        $lists_departments['list_departments'] = $list_departments;

        return $lists_departments;
    }


    // Функция которая получает список разрешенных правами департаментов на указанное право

    function getLS($entity_name, $method, $type_list) {

            // Получаем сессию
            $session  = session('access');
            if(!isset($session)){abort(403, 'Нет сессии!');};

            if(isset($session['all_rights'][$method . '-'. $entity_name .'-allow'][$type_list])) {

                // Получаем список отделов
                $departments = $session['all_rights'][$method . '-'. $entity_name .'-allow'][$type_list];

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
                $departments = $session['company_info'][$type_list];
            };
        };

        if(!isset($departments)){$departments = null;};
        return $departments;
    }

?>