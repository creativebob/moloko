<?php
        use App\Scopes\ModerationScope;

        function operator_right($entity_name, $entity_dependence) {


        // Получаем сессию
        $session  = session('access');

        if(!isset($session)){abort(403, 'Нет сессии!');};

        // Получаем список авторов
        $list_authors = $session['list_authors'];


        // ПРОВЕРЯЕМ ПРАВО НА ПРОСМОТР НЕ ОТМОДЕРИРОВАННЫХ ЗАПИСЕЙ  ------------------------------------------------------------------------------------------
        // Проверяем право просмотра системных записей:
        
        if(isset($session['all_rights']['moderator-'.$entity_name.'-allow']) && (!isset($session['all_rights']['moderator-'.$entity_name.'-deny'])))
        {

            // Не буду иметь возможность модерировать записи - не буду их видеть
            $moderator = ModerationScope::class;
            // dd($moderator);

        } else {

            // Буду видеть не отмодерированные записи и смогу их модерировать
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
            $dependence = true;
        };




        // ПРОВЕРЯЕМ ПРАВО НА ПРОСМОТР ОБЩЕГО СПИСКА  --------------------------------------------------------------------------------------------------------
        // Проверяем в правах (которые записаны в сессию) наличие права на просмотр общего списка пользователей 
        // и отсутствие запрета
        if(isset($session['all_rights']['index-'.$entity_name.'-allow']) && (!isset($session['all_rights']['index-'.$entity_name.'-deny'])))
        {

            // Получаем список ID филиалов в которых присутствует право на просмотр списка пользователей
            $filials = collect($session['all_rights']['index-'.$entity_name.'-allow']['departments'])->keys()->toarray();


            // Получаем список ID департаментов в которых присутствует право на просмотр списка пользователей
            $departments = collect($session['all_rights']['index-'.$entity_name.'-allow']['departments'])->values()->toarray();

        } else {

            // Если выборка не зависима
            $filials = null;
            $departments = null;
            abort(403, 'Ебать, прав то нет у тебя! Филиал или отдел не прописался в сессию.');
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

            // Разрешено ли использовать список авторов
            if($use_authors){

                // Проверяем - есть ли в сессии авторы?
                if(isset($session['all_rights']['authors-'.$entity_name.'-allow']['authors'])) 
                {

                    $authors = $session['all_rights']['authors-'.$entity_name.'-allow']['authors'];

                } else {

                    $list_authors['authors_id'] = null;
                    $authors = $list_authors;
                    abort(403, 'Не найден список авторов для пользователя!');

                };

            } else {

                // Если не разрешено использовать список авторов, то обнуляем список который пришел из сессии
                
                $list_authors['authors_id'] = null;
                $authors = $list_authors;
                $authors = $list_authors;  

            };

        } else {

            // Нет права смотреть чужие записи
            $list_authors['authors_id'] = null;
            $authors = $list_authors;
        };



        // ФОРМИРУЕМ РЕЗУЛЬТАТЫ И ОФОРМЛЯЕМ ИХ В ВИДЕ МАССИВА ДЛЯ ОТПРАВКИ В КОНТРОЛЛЕР

        $operator_info['dependence'] = $dependence;
        $operator_info['moderator'] = $moderator;
        $operator_info['system_item'] = $system_item;
        $operator_info['filials'] = $filials;
        $operator_info['departments'] = $departments;
        $operator_info['authors'] = $authors;
        $operator_info['automoderate'] = $automoderate;
        $operator_info['user_id'] = $session['user_info']['user_id'];
        $operator_info['company_id'] = $session['user_info']['company_id'];

        // dd($operator_info);

        return $operator_info;
    };

?>