<?php

    use App\Booklist;
    use App\List_item;
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

        $list_filter =[];
        $filter_name = $name;
        $model_entity_name = $name . '_name';


            $filter_entity = $request->user()->booklists_author->where('entity_alias', $entity_name)->values();

            if(count($filter_entity)>0){

                foreach($filter_entity as $booklist){
                    $list_filter['item_list'][$booklist->id] = $booklist->booklist_name;
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
            $booklists_default = $booklists_user->where('booklist_name', 'Default')->first();

            if($booklists_default != null){
            
                $booklists_default = $booklists_default->list_items->pluck('item_entity')->toArray();
                foreach ($booklists_user as $booklist){

                    $booklist_id = $booklist->id;

                    if($booklist->booklist_name == 'Default'){$booklists[$booklist_id]['status'] = 'Default';} else {$booklists[$booklist_id]['status'] = 'Simple';};

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

        $filter[$filter_name]['list_select'] = $list_filter; 
        $filter[$filter_name]['title'] = $title; // Назавние фильтра

        // dd($filter);
        return $filter;
    }


    function addFilter($filter, $filter_query, $request, $title, $name, $column, $entity_name = 'none'){

        $list_filter =[];
        $filter_name = $name;
        $model_entity_name = 'name';

        $filter_entity = $filter_query->unique($column); 

        if(count($filter_entity) > 0){

            foreach($filter_entity as $entity){
                $list_filter['item_list'][$entity->$name->id] = $entity->$name->name;
            }
        };

        $filter[$filter_name]['mode'] = 'id'; // Назавние фильтра
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

        $filter[$filter_name]['list_select'] = $list_filter; 
        $filter[$filter_name]['title'] = $title; // Назавние фильтра

        // dd($filter);
        return $filter;
    }

    function addCityFilter($filter, $filter_query, $request, $title, $name, $column, $entity_name = 'none'){

        $list_filter =[];
        $filter_name = $name;
        $model_entity_name = 'name';

        $filter_entity = $filter_query->unique('location.city_id');
        // dd($filter_entity);

        if(count($filter_entity) > 0){

            foreach($filter_entity as $entity){
                if(isset($entity->location->city_id)){
                    $list_filter['item_list'][$entity->location->city_id] = $entity->location->city->name;                    
                } else {
                    // $list_filter['item_list'][null] = 'Город не определен';
                };
            }
        };

        $filter[$filter_name]['mode'] = 'id'; // Назавние фильтра
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

        $filter[$filter_name]['list_select'] = $list_filter; 
        $filter[$filter_name]['title'] = $title; // Назавние фильтра

        // dd($filter);
        return $filter;
    }



    // Пилим checkboxer
    function getCheckboxerData($result_query, $title, $name, $column, $entity_name = 'none'){

        $checkboxer_mass = [];
        $filter_entity = $result_query->unique($column); 

            if(count($filter_entity)>0){

                // foreach($filter_entity as $entity){
                //     $list_filter['item_list'][$entity->$name->id] =  $entity->$name->$model_entity_name;
                // }
            };

            $checkboxer_mass['mode'] = 'id'; // Назавние фильтра

            $checkboxer_mass['collection'] = $filter_entity;

            if($request->$column == null){

                $checkboxer_mass['mass_id'] = null;
                $checkboxer_mass['count_mass'] = 0;
                
            } else {

                $checkboxer_mass['mass_id'] = $request->$column; // Получаем список ID
                if(is_array($request->$column)){
                    $checkboxer_mass['count_mass'] = count($request->$column);

                } else {
                    $checkboxer_mass['count_mass'] = 0;
                };
                
            };

            $checkboxer_mass['list_select'] = $list_filter; 
            $checkboxer_mass['title'] = $title; // Назавние фильтра

            // dd($filter);
            return $checkboxer_mass;

        };

?>