<?php

    use App\Booklist;
    use App\List_item;

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


    function addBooklist($filter, $request, $entity_name = 'none'){

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
                $filter['count'] = $filter['count'] + 1;

            } else {
                $filter[$filter_name]['count_mass'] = 0;
            };
            
        };

        $filter[$filter_name]['list_select'] = $list_select; 
        $filter[$filter_name]['title'] = $title; // Назавние фильтра
        $filter[$filter_name]['entity_alias'] = $entity_name; // Сущность буклиста

        return $filter;
    }

    function addFilter($filter, $filter_query, $request, $title, $filter_name, $column, $relations = null, $filter_mode = null){

        // Готовим массив для наполнения пунктами коллекции
        $list_select = [];
        if(!isset($filter['count'])){$filter['count'] = 0;};

        // Пишем режим в фильтр
        $filter[$filter_name]['mode'] = $filter_mode;
        $filter[$filter_name]['column'] = $column;

        // ОПРЕДЕЛЯЕМ РЕЖИМ РАБОТЫ ФИЛЬТРА И ФОРМИРУЕМ ВЫПАДАЮЩИЙ СПИСОК
 
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


        $filter[$filter_name]['collection'] = $filter_entity;

        if($request[$column] == null){

            $filter[$filter_name]['mass_id'] = null;
            $filter[$filter_name]['count_mass'] = 0;
            
        } else {

            $filter[$filter_name]['mass_id'] = $request[$column]; // Получаем список ID

            if(is_array($request[$column])){

                $filter[$filter_name]['count_mass'] = count($request[$column]);
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

        return $filter;

    }


    function setFilter($entity_name, $request, $filters){

        $filter['status'] = null;
        $filter['entity_name'] = $entity_name;
        $filter['inputs'] = $request->input();
        $filter['count'] = 0;

        foreach ($filters as $filter_name) {

            // Если фильтр не являеться спецефическим, то создаем его через общую функцию
            if(($filter_name != 'date_interval')&&($filter_name != 'booklist')){
                $filter = addMyFilter($filter, $request, $filter_name);
            }


            // Если фильтр специфичен: по дате
            if($filter_name == 'date_interval'){
                $filter = addFilterInterval($filter, $entity_name, $request, 'date_start', 'date_end');
            }

            // Если фильтр специфичен: буклист
            if($filter_name == 'booklist'){
                $filter = addBooklist($filter, $request, $entity_name);
            }

        }


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


    function addMyFilter($filter, $request, $name_filter){

        // ФИЛЬТР ПО КОМПАНИИ ---------------------------------------------------------
        if($name_filter == 'company'){

            $filter[$name_filter]['title'] = 'Компания:';                               // Назавние фильтра
            $column = 'company_id';                                                     // Имя переменной в request
            $filter[$name_filter]['list_select']['item_list'] = getFilterCompanyList(); // Функция с запросом
        }
        // ----------------------------------------------------------------------------

        // ФИЛЬТР ПО КЛИЕНТАМ ---------------------------------------------------------
        if($name_filter == 'client'){

            $filter[$name_filter]['title'] = 'Клиенты:';                                // Назавние фильтра
            $column = 'client_id';                                                      // Имя переменной в request
            $filter[$name_filter]['list_select']['item_list'] = getFilterClientList();  // Функция с запросом
        }
        // ----------------------------------------------------------------------------

        // ФИЛЬТР ПО ПОСТАВЩИКАМ ------------------------------------------------------
        if($name_filter == 'supplier'){

            $filter[$name_filter]['title'] = 'Поставщики:';                                 // Назавние фильтра
            $column = 'supplier_id';                                                        // Имя переменной в request
            $filter[$name_filter]['list_select']['item_list'] = getFilterSupplierList();    // Функция с запросом
        }
        // ----------------------------------------------------------------------------

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


        // ФИЛЬТР ПО МЕНЕДЖЕРУ --------------------------------------------------------
        if($name_filter == 'manager'){

            $filter[$name_filter]['title'] = 'Менеджер:';                                   // Назавние фильтра
            $column = 'manager_id';                                                         // Имя переменной в request
            $filter[$name_filter]['list_select']['item_list'] = getFilterManagerList();     // Функция с запросом
        }
        // ----------------------------------------------------------------------------


        // ФИЛЬТР ПО АВТОРУ -----------------------------------------------------------
        if($name_filter == 'author'){

            $filter[$name_filter]['title'] = 'Автор:';                                      // Назавние фильтра
            $column = 'author_id';                                                          // Имя переменной в request
            $filter[$name_filter]['list_select']['item_list'] = getFilterAuthorList();      // Функция с запросом
        }
        // ----------------------------------------------------------------------------


        // ФИЛЬТР ПО КАТЕГОРИИ ТОВАРА -------------------------------------------------
        if($name_filter == 'goods_category'){

            $filter[$name_filter]['title'] = 'Категория:';                                      // Назавние фильтра
            $column = 'category_id';                                                            // Имя переменной в request
            $filter[$name_filter]['list_select']['item_list'] = getFilterGoodsCategoryList();   // Функция с запросом
        }
        // ----------------------------------------------------------------------------


        // ФИЛЬТР ПО ГРУППЕ ТОВАРА ----------------------------------------------------
        if($name_filter == 'articles_group'){

            $filter[$name_filter]['title'] = 'Группа артикула:';                                // Назавние фильтра
            $column = 'articles_group_id';                                                      // Имя переменной в request
            $filter[$name_filter]['list_select']['item_list'] = getFilterArticlesGroupList();   // Функция с запросом
        }
        // ----------------------------------------------------------------------------


        // ФИЛЬТР ПО КАТЕГОРИИ УСЛУГИ -------------------------------------------------
        if($name_filter == 'services_category'){

            $filter[$name_filter]['title'] = 'Категория:';                                       // Назавние фильтра
            $column = 'services_category_id';                                                    // Имя переменной в request
            $filter[$name_filter]['list_select']['item_list'] = getFilterServicesCategoryList(); // Функция с запросом
        }
        // ----------------------------------------------------------------------------


        // ФИЛЬТР ПО ГРУППЕ УСЛУГИ ----------------------------------------------------
        if($name_filter == 'services_product'){

            $filter[$name_filter]['title'] = 'Группа услуги:';                                  // Назавние фильтра
            $column = 'services_product_id';                                                    // Имя переменной в request
            $filter[$name_filter]['list_select']['item_list'] = getFilterServicesProductList(); // Функция с запросом
        }
        // ----------------------------------------------------------------------------


        // ФИЛЬТР ПО КАТЕГОРИИ СЫРЬЯ --------------------------------------------------
        if($name_filter == 'raws_category'){

            $filter[$name_filter]['title'] = 'Категория:';                                       // Назавние фильтра
            $column = 'raws_category_id';                                                        // Имя переменной в request
            $filter[$name_filter]['list_select']['item_list'] = getFilterRawsCategoryList();     // Функция с запросом
        }
        // ----------------------------------------------------------------------------


        // ФИЛЬТР ПО ГРУППЕ СЫРЬЯ -----------------------------------------------------
        if($name_filter == 'raws_product'){

            $filter[$name_filter]['title'] = 'Группа сырья:';                                  // Назавние фильтра
            $column = 'raws_product_id';                                                       // Имя переменной в request
            $filter[$name_filter]['list_select']['item_list'] = getFilterRawsProductList();    // Функция с запросом
        }
        // ----------------------------------------------------------------------------


        // ФИЛЬТР ПО ОТВЕТСТВЕННОМУ ПО ЗАДАЧИ -----------------------------------------
        if($name_filter == 'appointed'){

            $filter[$name_filter]['title'] = 'Исполнитель:';                                 // Назавние фильтра
            $column = 'appointed_id';                                                        // Имя переменной в request
            $filter[$name_filter]['list_select']['item_list'] = getFilterAppointedList();    // Функция с запросом
        }
        // ----------------------------------------------------------------------------


        // ФИЛЬТР ПО ДОЛЖНОСТИ --------------------------------------------------------
        if($name_filter == 'position'){

            $filter[$name_filter]['title'] = 'Должность:';                                     // Назавние фильтра
            $column = 'position_id';                                                           // Имя переменной в request
            $filter[$name_filter]['list_select']['item_list'] = getFilterPositionList();       // Функция с запросом
        }
        // ----------------------------------------------------------------------------


        // ФИЛЬТР ПО ОТДЕЛУ -----------------------------------------------------------
        if($name_filter == 'department'){

            $filter[$name_filter]['title'] = 'Отделы:';                                     // Назавние фильтра
            $column = 'department_id';                                                      // Имя переменной в request
            $filter[$name_filter]['list_select']['item_list'] = getFilterDepartmentList();  // Функция с запросом
        }
        // ----------------------------------------------------------------------------


        // ФИЛЬТР ПО ТИПУ ПОМЕЩЕНИЯ  ---------------------------------------------------
        if($name_filter == 'places_type'){

            $filter[$name_filter]['title'] = 'Тип помещения:';                              // Назавние фильтра
            $column = 'places_type_id';                                                     // Имя переменной в request
            $filter[$name_filter]['list_select']['item_list'] = getFilterPlacesTypeList();  // Функция с запросом
        }
        // ----------------------------------------------------------------------------


        // ФИЛЬТР ПО СЕКТОРУ (НАПРАВЛЕНИЮ ДЕЯТЕЛЬНОСТИ) -------------------------------
        if($name_filter == 'sector'){

            $filter[$name_filter]['title'] = 'Сектор:';                                     // Назавние фильтра
            $column = 'sector_id';                                                          // Имя переменной в request
            $filter[$name_filter]['list_select']['item_list'] = getFilterSectorList();      // Функция с запросом
        }
        // ----------------------------------------------------------------------------


        // ФИЛЬТР ПО СТАТУСУ ЗАДАЧИ ---------------------------------------------------
        if($name_filter == 'challenge_status'){

            $filter[$name_filter]['title'] = 'Статус задачи:';                                  // Назавние фильтра
            $column = 'challenge_status';                                                       // Имя переменной в request
            $filter[$name_filter]['list_select']['item_list'] = ['Выполнена', 'Не выполнена'];  // Генерируем список 
        }
        // ----------------------------------------------------------------------------


        // ФИЛЬТР ПО СТРАНЕ ПРОИЗВОДИТЕЛЮ ---------------------------------------------
        if($name_filter == 'manufacturer_country'){

            $filter[$name_filter]['title'] = 'Страна производителя:';                                   // Назавние фильтра
            $column = 'country_id';                                                                     // Имя переменной в request
            $filter[$name_filter]['list_select']['item_list'] = getFilterManufacturerCountryList();     // Генерируем список 
        }
        // ----------------------------------------------------------------------------


        // ФИЛЬТР ПО АКТИВНЫМ ЗАДАЧАМ---- ---------------------------------------------
        if($name_filter == 'challenges_active_count'){

            $filter[$name_filter]['title'] = 'Активные задачи:';                                        // Назавние фильтра
            $column = 'challenges_active_count';                                                        // Имя переменной в request
            $filter[$name_filter]['list_select']['item_list'] = getFilterCACList();                     // Генерируем список
        }
        // ----------------------------------------------------------------------------


        // ФИЛЬТР ПО ТИПУ ПОЛЬЗОВАТЕЛЯ ------------------------------------------------
        if($name_filter == 'user_type'){

            $filter[$name_filter]['title'] = 'Тип пользователя:';
            $column = 'user_type';
            $filter[$name_filter]['list_select']['item_list'] = getFilterUserTypeList();
        }
        // ----------------------------------------------------------------------------


        // ФИЛЬТР ПО БЛОКИРОВКЕ ПОЛЬЗОВАТЕЛЯ ------------------------------------------
        if($name_filter == 'access_block'){

            $filter[$name_filter]['title'] = 'Доступ:';
            $column = 'access_block';
            $filter[$name_filter]['list_select']['item_list'] = getFilterAccessBlockList();
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

        return $filter;

    }

    function getFilterCompanyList(){

        $companies = App\Company::orderBy('name', 'asc')
        ->get()->pluck('name', 'id')->toArray();
        return $companies;
    }

    function getFilterClientList(){

        $clients = App\Client::with('client')->get()->pluck('client.name', 'id')->toArray();
        return $clients;
    }

    function getFilterSupplierList(){

        $suppliers = App\Supplier::with('company')->get()->pluck('company.name', 'company.id')->toArray();
        return $suppliers;
    }

    function getFilterCityList(){

        $cities = App\City::orderBy('name', 'asc')
        ->get()->pluck('name', 'id')->toArray();
        return $cities;
    }

    function getFilterStageList(){

        $stages = App\Stage::orderBy('name', 'asc')
        ->get()->pluck('name', 'id')->toArray();
        return $stages;
    }

    function getFilterLeadMethodList(){

        $lead_methods = App\LeadMethod::orderBy('name', 'asc')
        ->get()->pluck('name', 'id')->toArray();
        return $lead_methods;
    }

    function getFilterManagerList(){

        $leads = App\Lead::whereNull('draft')->pluck('manager_id');
        $managers_id = $leads->unique()->toArray();

        $managers = App\User::whereIn('id', $managers_id)->get()->sortByDesc('name_reverse')->pluck('nameReverse', 'id')->toArray();

        if(isset($managers)){asort($managers);}
        return $managers;

    }

    function getFilterAuthorList(){

        $employees = App\Employee::with('user')->get()->pluck('user.name', 'user.id')->toArray();
        if(isset($employees)){asort($employees);}
        return $employees;

    }

    function getFilterLeadTypeList(){

        $lead_types = App\LeadType::orderBy('name', 'asc')
        ->get()->pluck('name', 'id')->toArray();
        return $lead_types;

    }

    function getFilterGoodsCategoryList(){

        $goods_categories = App\GoodsCategory::orderBy('name', 'asc')
        ->get()->pluck('name', 'id')->toArray();
        return $goods_categories;
    }

    function getFilterArticlesGroupList(){

        $articles_group = App\ArticlesGroup::orderBy('name', 'asc')
        ->get()->pluck('name', 'id')->toArray();
        return $articles_group;
    }

    function getFilterServicesCategoryList(){

        $services_categories = App\ServicesCategory::orderBy('name', 'asc')
        ->get()->pluck('name', 'id')->toArray();
        return $services_categories;
    }

    function getFilterServicesProductList(){

        $services_products = App\ServicesProduct::orderBy('name', 'asc')
        ->get()->pluck('name', 'id')->toArray();
        return $services_products;
    }

    function getFilterRawsCategoryList(){

        $raws_categories = App\RawsCategory::orderBy('name', 'asc')
        ->get()->pluck('name', 'id')->toArray();
        return $raws_categories;
    }

    function getFilterRawsProductList(){

        $raws_products = App\RawsProduct::orderBy('name', 'asc')
        ->get()->pluck('name', 'id')->toArray();
        return $raws_products;
    }

    function getFilterAppointedList(){

        // $appointeds = App\Challenge::with('appointed')->get()->pluck('appointed.nameReverse', 'appointed.id')->toArray();
        // if(isset($appointeds)){asort($appointeds);}

        $appointeds = App\Employee::with('user')->get()->pluck('user.name', 'user.id')->toArray();
        if(isset($appointeds)){asort($appointeds);}
        return $appointeds;


        return $appointeds;
    }

    function getFilterPositionList(){

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('positions', false, 'index');

        $positions = App\Position::moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
        ->systemItem($answer)
        ->template($answer)
        ->orderBy('name', 'asc')->get()->pluck('name', 'id')->toArray();
        return $positions;
    }

    function getFilterDepartmentList(){

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('departments', true, 'index');

        $departments = App\Department::moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
        ->systemItem($answer)
        ->template($answer)
        ->orderBy('name', 'asc')->get()->pluck('name', 'id')->toArray();
        return $departments;
    }


    function getFilterSectorList(){

        $sectors = App\Sector::orderBy('name', 'asc')->get()->pluck('name', 'id')->toArray();
        return $sectors;
    }


    function getFilterPlacesTypeList(){

        $places_types = App\PlacesType::orderBy('name', 'asc')
        ->get()->pluck('name', 'id')->toArray();
        return $places_types;

    }

    function getFilterManufacturerCountryList(){

        $country_id = App\Manufacturer::with('company.location.country')->get()->pluck('company.location.country.id')->toArray();
        $countries = App\Country::whereIn('id', $country_id)->get()->sortByDesc('name')->pluck('name', 'id')->toArray();
        if(isset($countries)){asort($countries);}
        return $countries;

    }

    function getFilterCACList(){
        $array = [
            1 => 'Есть активные задачи',
            0 => 'Нет задач'
        ];

        return $array;
    }

    function getFilterUserTypeList(){
        $array = [
            0 => 'Чужой',
            1 => 'Свой'
        ];

        return $array;
    }

    function getFilterAccessBlockList(){
        $array = [
            0 => 'Открыт',
            1 => 'Блокирован'
        ];

        return $array;
    }

?>