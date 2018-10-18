<?php

    use App\Booklist;
    use App\List_item;
    use App\City;
    use App\Stage;
    use App\Lead;    
    use App\LeadMethod;   
    use App\LeadType;

    // Куки
    use Illuminate\Support\Facades\Cookie;

    //Формируем необходимые для фильтра выпадающие списки и прочее
    // --------------------------------------------------------------------------------------------------------------------
    // КОМПАНИЯ -----------------------------------------------------------------------------------------------------------
    // --------------------------------------------------------------------------------------------------------------------


    function getListFilterAuthor($filter_query){

        $filter['authors_list'] = [];
        $companies_authors_filter = $filter_query->unique('author_id');
        // $filter['cities_list'][0] =  '- не выбрано -';
        if(count($companies_authors_filter)>0){
            foreach($companies_authors_filter as $company){

                if($company->author != null){
                    $filter['authors_list'][$company->author->id] =  $company->author->first_name;
                };
            }
        };

        // dd($filter);
        return $filter;
    }


    //Формируем необходимые для фильтра выпадающие списки и прочее
    // --------------------------------------------------------------------------------------------------------------------
    // ПОЛЬЗОВАТЕЛЬ -------------------------------------------------------------------------------------------------------
    // --------------------------------------------------------------------------------------------------------------------
    
    function getFilterUser($filter_query){

        $user_cities_filter = $filter_query->unique('city_id');
        $filter['cities_list'][0] =  '- не выбрано -';

            foreach($user_cities_filter as $user){
                $filter['cities_list'][$user->city->id] = $user->city->city_name;
            }

        return $filter;
    }


    function addBooklist($filter, $filter_query, $request, $entity_name = 'none'){

        $title = 'Мои списки:';
        $name = 'booklist';
        $column = 'booklist_id';

        $list_select =[];
        $filter_name = $name;

            $filter_entity = $request->user()->booklists_author->where('entity_alias', $entity_name)->values();

            if(count($filter_entity)>0){

                foreach($filter_entity as $booklist){
                    $list_select['item_list'][$booklist->id] = $booklist->name;
                }

            };

            $filter[$filter_name]['mode'] = 'model'; // Назавние фильтра

            // СОЗДАЕМ МАССИВЫ СПИСКОВ ДЛЯ БУКЛИСТОВ

            $booklists_user = Booklist::with('list_items')
            ->where('author_id', $request->user()->id)
            ->where('entity_alias', $entity_name)
            ->orderBy('created_at', 'desc')
            ->get();

            // dd($booklists_user);

            $booklists = [];

            // sortByDesc('id')

            // Получаем список Default
            $booklists_default = $booklists_user->where('name', 'Default')->first();

            if($booklists_default != null){
            
                $booklists_default = $booklists_default->list_items->pluck('item_entity')->toArray();
                foreach ($booklists_user as $booklist){

                    $booklist_id = $booklist->id;

                    if($booklist->name == 'Default'){$booklists[$booklist_id]['status'] = 'Default';} else {$booklists[$booklist_id]['status'] = 'Simple';};

                    $booklists['default'] = $booklists_default;
                    $booklists['default_count'] = count($booklists_default);
                    $booklists['request_mass'] = $request->booklist_id;


                    $booklists[$booklist_id]['collection'] = $booklist;

                    $booklists[$booklist_id]['mass_items'] = $booklist->list_items->pluck('item_entity')->toArray();
                    $booklists[$booklist_id]['mass_count'] = count($booklist->list_items->pluck('item_entity')->toArray());

                    $booklists[$booklist_id]['plus'] = collect($booklists_default)->diff($booklists[$booklist_id]['mass_items'])->count();
                    $booklists[$booklist_id]['plus_mass'] = collect($booklists_default)->diff($booklists[$booklist_id]['mass_items']);

                    $booklists[$booklist_id]['minus'] = collect($booklists[$booklist_id]['mass_items'])->intersect($booklists_default)->count();
                    $booklists[$booklist_id]['minus_mass'] = collect($booklists[$booklist_id]['mass_items'])->intersect($booklists_default);

                }

                $filter[$filter_name]['booklists'] = $booklists;
                // dd($filter);

            } else {

            // Если списка Default нет, то пишем null
                $filter[$filter_name]['booklists'] = null;
            }


        $filter[$filter_name]['collection'] = $filter_entity;

        if($request->$column == null){

            $filter[$filter_name]['mass_id'] = null;
            $filter[$filter_name]['count_mass'] = 0;
            
        } else {

            $filter[$filter_name]['mass_id'] = $request->$column; // Получаем список ID
            if(is_array($request->$column)){
                $filter[$filter_name]['count_mass'] = count($request->$column);
                $filter['status'] = 'active';

            } else {
                $filter[$filter_name]['count_mass'] = 0;
            };
            
        };

        $filter[$filter_name]['list_select'] = $list_select; 
        $filter[$filter_name]['title'] = $title; // Назавние фильтра

        return $filter;
    }


    // $filter - массив / тело фильтра


    function addFilter($filter, $filter_query, $request, $title, $filter_name, $column, $relations = null, $filter_mode = null, $checkboxer_mode = 'filter'){

        // Готовим массив для наполнения пунктами коллекции
        $list_select = [];
        if(!isset($filter['count'])){$filter['count'] = 0;};

        // Пишем режим в фильтр
        $filter[$filter_name]['mode'] = $filter_mode;
        $filter[$filter_name]['column'] = $column;

        // ОПРЕДЕЛЯЕМ РЕЖИМ РАБОТЫ ФИЛЬТРА И ФОРМИРУЕМ ВЫПАДАЮЩИЙ СПИСОК
 
        // Если режим не передали - пытаемся определить самостоятельно
        if($filter_mode == null){

            // Если выборка в текущей колекции или 
            if($relations != null){

                // Выбираем только уникальные ID
                $filter_entity = $filter_query->unique($relations . '.' . $column);

                if($column == 'id'){

                    // Выборка по ID в третей коллекции
                    $filter[$filter_name]['mode'] = 'external-self-one';
                } else {

                    // Выборка элементов (id и name) из третей коллекции по присутствию ID ее элементов во второй коллекции
                    // по присутствию ID ее элементов в первой коллекции
                    $filter[$filter_name]['mode'] = 'external-id-one';
                };

            } else {

                // Выбираем только уникальные ID
                $filter_entity = $filter_query->unique($column);

                if($column == 'id'){

                    // Выборка элементов по ID текущей коллекции
                    $filter[$filter_name]['mode'] = 'internal-self-one';
                } else {

                    // Выборка элементов из другой коллекции по присутствию ID ее элементов в текущей коллекции
                    $filter[$filter_name]['mode'] = 'internal-id-one';
                };

            };

        } else {


            // Вытаскиеваем в зависимости от режима нужную коллекцию
            if($filter_mode == 'internal-self-one'){



                // Выбираем только уникальные ID
                $filter_entity = $filter_query->unique($column);

                if(count($filter_entity) > 0){
                    foreach($filter_entity as $entity){
                        $list_select['item_list'][$entity->id] = $entity->name;
                    }
                };

                $column = $filter_name . "_" . $column;
                // dd($column);
                
            };

            if($filter_mode == 'internal-id-one'){

                // Выбираем только уникальные ID
                $filter_entity = $filter_query->unique($column);

                if(count($filter_entity) > 0){
                    foreach($filter_entity as $entity){
                        $list_select['item_list'][$entity->$filter_name->id] = $entity->$filter_name->name;
                    }
                }

            };


            // Вытаскиеваем в зависимости от режима нужную коллекцию
            if($filter_mode == 'external-id-one'){

                // dd($column);
                // Выбираем только уникальные ID

                $filter_entity = $filter_query->unique($relations . '.' . $column);
                // dd($filter_entity);

                if(count($filter_entity) > 0){
                    foreach($filter_entity as $entity){

                        if(empty($entity->$relations)){
                            $list_select['item_list'][null] = 'Не указано';
                        } else {
                            if(isset($entity->$relations->$filter_name->name)){
                                $list_select['item_list'][$entity->$relations->$column] = $entity->$relations->$filter_name->name;
                            }
                        };
                    }
                }
                
                if(isset($list_select['item_list'])){asort($list_select['item_list']);}
            };
            
            if($filter_mode == 'external-id-many'){

                $filter_entity = $filter_query->unique($relations);

                // Выбираем только уникальные ID
                foreach($filter_entity as $item){
                    if($item->$relations->isEmpty()){
                        $list_select['item_list'][null] = 'Не указано';
                    } else {
                        foreach($item->$relations as $item2){
                            $list_select['item_list'][$item2->id] = $item2->name;
                        }
                    }
                }
            };


            if($filter_mode == 'external-self-one'){

                // Выбираем только уникальные ID
                $filter_entity = $filter_query->unique($relations . '.' . $column);

                if(count($filter_entity) > 0){
                    foreach($filter_entity as $entity){
                            $list_select['item_list'][$entity->$relations->$column] = $entity->$relations->name;
                    }
                }

            };


            // Вытаскиеваем в зависимости от режима нужную коллекцию
            if($filter_mode == 'external-id-one-one'){

                $array_relations = explode(".", $relations);
                $relations_1 = $array_relations[0];
                $relations_2 = $array_relations[1];

                // Выбираем только уникальные ID

                $filter_entity = $filter_query->unique($relations_1 . '.' . $relations_2 . '.' . $column);

                if(count($filter_entity) > 0){
                    foreach($filter_entity as $entity){
                        $list_select['item_list'][$entity->$relations_1->$relations_2->$column] = $entity->$relations_1->$relations_2->$filter_name->name;
                        if($entity->$relations_1->$relations_2 == null){$list_select['item_list'][null] = 'Не указано';};
                    }
                }

            };


            // Вытаскиеваем в зависимости от режима нужную коллекцию
            // if($filter_mode == 'internal-text'){

            //     // Выбираем только уникальные ID
            //     $filter_entity = $filter_query->unique($column);

            //     if(count($filter_entity) > 0){
            //         foreach($filter_entity as $entity){
            //             $list_select['item_list'][$entity->$column] = $entity->$column;
            //         }
            //     };

            // };


        };

        $filter[$filter_name]['collection'] = $filter_entity;

        if($request->$column == null){

            $filter[$filter_name]['mass_id'] = null;
            $filter[$filter_name]['count_mass'] = 0;
            
        } else {

            $filter[$filter_name]['mass_id'] = $request->$column; // Получаем список ID


            if(is_array($request->$column)){

                $filter[$filter_name]['count_mass'] = count($request->$column);
                $filter['status'] = 'active';
                $filter['count'] = $filter['count'] + 1;

            } else {
                $filter[$filter_name]['count_mass'] = 0;
            };
            
        };

        $filter[$filter_name]['column'] = $column;
        $filter[$filter_name]['list_select'] = $list_select; 
        $filter[$filter_name]['title'] = $title; // Назавние фильтра


        if(count($filter[$filter_name]['list_select']) == 0){
            $filter[$filter_name] = null;
        }

        $filter_entity_name = $filter['entity_name'];

        // Если работаем в режиме фильтра - будем записывать куки
        if($checkboxer_mode == 'filter'){

            if($filter['count'] > 0) {
                
                // Пишем в куку
                $filter_url = $request->fullUrl();
                Cookie::queue('filter_' . $filter_entity_name, $filter_url, 1440);
                
            } else {

                // Удаляем куку
                Cookie::queue(Cookie::forget('filter_' . $filter_entity_name)); 
            };
        };


        return $filter;

    }


    function autoFilter($request, $entity_name){

            if($request->cookie('filter_' . $entity_name) != null) {

                if($request->filter == 'disable'){

                    Cookie::queue(Cookie::forget('filter_' . $entity_name));
                    $filter_url = null;
                    return $filter_url;
                };

            $filter_url = Cookie::get('filter_' . $entity_name);
            // dd($filter_url);
            return $filter_url;
        };

    }


    function addFilterInterval($filter, $filter_entity_name, $request, $column_begin, $column_end){

        if(!isset($filter['count'])){$filter['count'] = 0;};

            if((isset($request->$column_begin))||(isset($request->$column_end))){
                $filter['count'] = $filter['count'] + 1;
            }

            if($filter['count'] > 0) {
                
                // Пишем в куку
                $filter_url = $request->fullUrl();
                Cookie::queue('filter_' . $filter_entity_name, $filter_url, 1440);

                $filter['status'] = 'active';
            } else {

                $filter['status'] = 'disable';
                // Удаляем куку
                Cookie::queue(Cookie::forget('filter_' . $filter_entity_name)); 
            };

        return $filter;

    }


    function addMyFilter($filter, $request, $name_filter){

        if(!isset($filter['count'])){$filter['count'] = 0;};

        // // Пишем режим в фильтр
        // $filter[$filter_name]['mode'] = $filter_mode;
        // $filter[$filter_name]['column'] = $column;




        // ФИЛЬТР ПО ГОРОДУ ------------------------------------------------------------
        if($name_filter == 'city'){

            $filter[$name_filter]['title'] = 'Выберите город:';                         // Назавние фильтра
            $column = 'city_id';                                                        // Имя переменной в request
            $filter[$name_filter]['list_select']['item_list'] = getFilterCityList();    // Функция с запросом
        }
        // ----------------------------------------------------------------------------


        // ФИЛЬТР ПО ЭТАПУ ------------------------------------------------------------
        if($name_filter == 'stage'){

            $filter[$name_filter]['title'] = 'Выберите этап:';                           // Назавние фильтра
            $column = 'stage_id';                                                        // Имя переменной в request
            $filter[$name_filter]['list_select']['item_list'] = getFilterStageList();    // Функция с запросом
        }
        // ----------------------------------------------------------------------------


        // ФИЛЬТР ПО МЕТОДУ ОБРАЩЕНИЯ -------------------------------------------------
        if($name_filter == 'lead_method'){

            $filter[$name_filter]['title'] = 'Способ обращения:';                           // Назавние фильтра
            $column = 'lead_method_id';                                                     // Имя переменной в request
            $filter[$name_filter]['list_select']['item_list'] = getFilterLeadMethodList();  // Функция с запросом
        }
        // -----------------------------------------------------------------------------


        // ФИЛЬТР ПО ТИПУ ОБРАЩЕНИЯ  ---------------------------------------------------
        if($name_filter == 'lead_type'){

            $filter[$name_filter]['title'] = 'Тип обращения:';                              // Назавние фильтра
            $column = 'lead_type_id';                                                       // Имя переменной в request
            $filter[$name_filter]['list_select']['item_list'] = getFilterLeadTypeList();    // Функция с запросом
        }
        // ----------------------------------------------------------------------------


        // ФИЛЬТР ПО МЕНЕДЖЕРУ ------------------------------------------------------------
        if($name_filter == 'manager'){

            $filter[$name_filter]['title'] = 'Менеджер:';                                   // Назавние фильтра
            $column = 'manager_id';                                                         // Имя переменной в request
            $filter[$name_filter]['list_select']['item_list'] = getFilterManagerList();     // Функция с запросом
        }
        // ----------------------------------------------------------------------------



        // ОБЩИЕ ДЛЯ ФИЛЬТРА НАСТРОЙКИ ====================================================

        // Проверка на пустоту данных которые пришли из URL по текущему фильтру
        if($request->$column != null){

            // Если ЕСТЬ фильтующая переменная в url
            // Подсчитываем количество элементов в массиве
            $filter[$name_filter]['count_mass'] = count($request->$column);

            // Общий счетчик фильтров увеличиваем
            $filter['count'] = $filter['count'] + 1;

        } else {

            // Если НЕТ фильтующей переменной в url
            $filter[$name_filter]['count_mass'] = 0;
        }

        // Собираем массив с фильтрующими данными
        $filter[$name_filter]['mass_id'] = $request->$column;

        // Сохраняем имя поля фильтра в контейнер фильтра
        $filter[$name_filter]['column'] = $column;

        // Запоминание фильтра
        if($filter['count'] > 0) {
            
            // Пишем в куку
            $filter_url = $request->fullUrl();
            Cookie::queue('filter_' . $filter['entity_name'], $filter_url, 1440);
            $filter['status'] = 'active';

        } else {

            $filter['status'] = 'disable';
            // Удаляем куку
            Cookie::queue(Cookie::forget('filter_' . $filter['entity_name'])); 
        }

        return $filter;
    }


    function getFilterCityList(){

        $cities = City::orderBy('name', 'asc')
        ->get()->pluck('name', 'id')->toArray();
        return $cities;
    }

    function getFilterStageList(){

        $stages = Stage::orderBy('name', 'asc')
        ->get()->pluck('name', 'id')->toArray();
        return $stages;
    }

    function getFilterLeadMethodList(){

        $lead_methods = LeadMethod::orderBy('name', 'asc')
        ->get()->pluck('name', 'id')->toArray();
        return $lead_methods;
    }

    function getFilterManagerList(){

        $managers = Lead::with('manager')->orderBy('name', 'asc')->get()->pluck('manager.name', 'manager.id');
        return $managers;

    }

    function getFilterLeadTypeList(){

        $lead_types = LeadType::orderBy('name', 'asc')
        ->get()->pluck('name', 'id')->toArray();
        return $lead_types;

    }

?>