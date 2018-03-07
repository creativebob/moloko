<?php

    //Формируем необходимые для фильтра выпадающие списки и прочее
    // --------------------------------------------------------------------------------------------------------------------
    // КОМПАНИЯ -----------------------------------------------------------------------------------------------------------
    // --------------------------------------------------------------------------------------------------------------------
    
    function getListFilterCompany($filter_query){

        $companies_cities_filter = $filter_query->unique('city_id');
        // $filter['cities_list'][0] =  '- не выбрано -';

        foreach($companies_cities_filter as $company){
            $filter['cities_list'][$company->city->id] =  $company->city->city_name;
        }

        return $filter;
    }

    function getListFilterAuthor($filter_query){

        $companies_authors_filter = $filter_query->unique('author_id');
        // $filter['cities_list'][0] =  '- не выбрано -';

        foreach($companies_authors_filter as $company){
            // dd($company);
            $filter['authors_list'][$company->author->id] =  $company->author->first_name;
        }

        return $filter;
    }


    //Формируем необходимые для фильтра выпадающие списки и прочее
    // --------------------------------------------------------------------------------------------------------------------
    // ПОЛЬЗОВАТЕЛЬ -------------------------------------------------------------------------------------------------------
    // --------------------------------------------------------------------------------------------------------------------
    
    function getFilterUser($filter_query){

        $user_cities_filter = $filter_query->unique('city_id');
        $filter['cities_list'][0] =  '- не выбрано -';

        // dd($filter);
        foreach($user_cities_filter as $user){
            $filter['cities_list'][$user->city->id] = $user->city->city_name;
        }

        return $filter;
    }


?>