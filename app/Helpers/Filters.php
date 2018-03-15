<?php

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


    function addFilter($filter, $filter_query, $request, $title, $name, $column, $entity_name = 'none'){

        $list_filter =[];
        $filter_name = $name;
        $model_entity_name = $name . '_name';

        // Только для Booklist
        if($name == 'booklist'){

            $filter_entity = $request->user()->booklists_author->where('entity_alias', $entity_name)->values();
            // dd($filter_entity);

            if(count($filter_entity)>0){

                foreach($filter_entity as $booklist){
                    $list_filter['item_list'][$booklist->id] = $booklist->booklist_name;
                }

            };

            $filter[$filter_name]['mode'] = 'model'; // Назавние фильтра

        } else {

            $filter_entity = $filter_query->unique($column); 
            if(count($filter_entity)>0){

                foreach($filter_entity as $entity){
                    $list_filter['item_list'][$entity->$name->id] =  $entity->$name->$model_entity_name;
                }
            };


            $filter[$filter_name]['mode'] = 'id'; // Назавние фильтра
        };

        // dd($filter_entity);

        $filter[$filter_name]['collection'] = $filter_entity;

        if($request->$column == null){

            $filter[$filter_name]['mass_id'] = null; // Получаем список ID городов
            $filter[$filter_name]['count_mass'] = 0;
        } else {

            $filter[$filter_name]['mass_id'] = $request->$column; // Получаем список ID
            $filter[$filter_name]['count_mass'] = count($request->$column);
        };


        $filter[$filter_name]['list_select'] = $list_filter; 
        $filter[$filter_name]['title'] = $title; // Назавние фильтра


        return $filter;
    }


?>