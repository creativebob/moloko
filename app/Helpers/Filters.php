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


    function addFilter($filter, $filter_query, $request, $title, $name, $entity){

        $filter_entity = $filter_query->unique($entity);

        $filter_name = $name;
        $filter[$filter_name]['collection'] = $filter_entity;

        if($request->$entity == null){
            $filter[$filter_name]['mass_id'] = null; // Получаем список ID городов
            $filter[$filter_name]['count_mass'] = 0;
        } else {
            $filter[$filter_name]['mass_id'] = $request->$entity; // Получаем список ID городов
            $filter[$filter_name]['count_mass'] = count($request->$entity);
        };

        $entity_name = $name . '_name';

        if(count($filter_entity)>0){
            foreach($filter_entity as $entity){
                $list_filter['item_list'][$entity->$name->id] =  $entity->$name->$entity_name;
            }
        };

        $filter[$filter_name]['list_select'] = $list_filter; 
        $filter[$filter_name]['title'] = $title; // Назавние фильтра

        return $filter;
    }


?>