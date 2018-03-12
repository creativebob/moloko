<?php

    //Формируем необходимые для фильтра выпадающие списки и прочее
    // --------------------------------------------------------------------------------------------------------------------
    // КОМПАНИЯ -----------------------------------------------------------------------------------------------------------
    // --------------------------------------------------------------------------------------------------------------------
    
    function getListFilterCompany($filter_query){

        $companies_cities_filter = $filter_query->unique('city_id');
        // $filter['cities_list'][0] =  '- не выбрано -';
        if(count($companies_cities_filter)>0){

            foreach($companies_cities_filter as $company){
                $filter['cities_list'][$company->city->id] =  $company->city->city_name;
            }

        };
        return $filter;
    }

    function getListFilterSector($filter_query){

        $companies_sectors_filter = $filter_query->unique('sector_id');
        // $filter['cities_list'][0] =  '- не выбрано -';
        if(count($companies_sectors_filter)>0){

            foreach($companies_sectors_filter as $company){
                $filter['sectors_list'][$company->sector->id] =  $company->sector->sector_name;
            }

        };
        return $filter;
    }


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


?>