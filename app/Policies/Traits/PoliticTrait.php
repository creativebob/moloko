<?php

namespace App\Policies\Traits;

// Прочие необходимые классы
use Illuminate\Support\Facades\Log;

trait PoliticTrait
{

    // Фильтрация для показа авторов
    public function getstatus($entity_name, $model, $method, $entity_dependence)
    {

        // Получаем данные из сессии
        $session  = session('access');
        $user_id = $session['user_info']['user_id'];
        $user_status = $session['user_info']['user_status'];
        $result = false;
        $right_dep_status = false;
        $filial_id = $session['user_info']['filial_id'];

        if(((!isset($session['user_info']['company_id']))||(!isset($session['user_info']['filial_id']))||(!isset($session['user_info']['department_id'])))&&($user_status == null)){return false;};

        // Пишем богу company_id = null
        if(isset($session['user_info']['company_id'])){$company_id = $session['user_info']['company_id'];} else {$company_id = null;};

        // Бог авторизованный под компанией может редактировать
        if(($user_status == 1)&&($method == 'update')&&($company_id != null)){

            if(isset($session['company_info']['filials'])){
                return true;            
            } else {
                return false;
                // abort(403, "Для начала создайте филиал!");
            };
        };

        // Предупреждаем божественное влияние на create!
        if(($company_id == null)&&(($method == 'create')||($method == 'update'))){

            // Разрешаем богу кое что редактировать без компании
            if(
                ($entity_name == 'pages')||
                ($entity_name == 'sities')||
                ($entity_name == 'navigations')||
                ($entity_name == 'menus')||
                ($entity_name == 'roles')||
                ($entity_name == 'companies')||
                ($entity_name == 'cities')||   
                ($entity_name == 'regions')||   
                ($entity_name == 'areas')||       
                ($entity_name == 'positions')||
                ($entity_name == 'entities')
            ){

            // Запрещаем редактировать остальные сущности
            } else {
                return false;
            };
        };


        //-----------------------------------------------------------------------------------------------------------------------------------------------------------
        // ОБЩИЕ ПРОВЕРКИ ДО ПРОВЕРКИ ОСНОВНЫХ ПРАВ -----------------------------------------------------------------------------------------------------------------
        //-----------------------------------------------------------------------------------------------------------------------------------------------------------

        // Общие проверки права для создания (Для обычного пользователя)
        // Запрещаем создание записей если нет компании или филиала
        if(($method == 'create')&&($user_status == 0)){

            // Если нет компании
            if($company_id == null){
                    // abort(403, 'Авторизуйтесь под компанией для создания записи');
                    return false;
            } else {

                if(!isset($session['company_info']['filials'])){
                    // abort(403, 'Для начала необходимо создать филиал! ;)');
                    return false;
                };
            };
        };


        // //-----------------------------------------------------------------------------------------------------------------------------------------------------------
        // // MODERATOR  -----------------------------------------------------------------------------------------------------------------------------------------------
        // //-----------------------------------------------------------------------------------------------------------------------------------------------------------

        // if($method == 'moderator'){
        //     if(isset($session['all_rights'][$method . '-'. $entity_name .'-allow'])) {

        //         // Нет ли блокировки этого права?
        //         if(!isset($session['all_rights'][$method . '-'. $entity_name .'-deny'])) {

        //             //Разрешаем, так как блокировки нет!
        //             return true;
                    
        //         } else {

        //         return false;

        //         };

        //     } else {
        //         return false;

        //     };
        // };



        // //-----------------------------------------------------------------------------------------------------------------------------------------------------------
        // // AUTOMODERATE  --------------------------------------------------------------------------------------------------------------------------------------------
        // //-----------------------------------------------------------------------------------------------------------------------------------------------------------

        // if($method == 'automoderate'){
        //     if(isset($session['all_rights'][$method . '-'. $entity_name .'-allow'])) {

        //         // Нет ли блокировки этого права?
        //         if(!isset($session['all_rights'][$method . '-'. $entity_name .'-deny'])) {

        //             //Разрешаем, так как блокировки нет!
        //             return true;
                    
        //         } else {

        //         return false;

        //         };

        //     } else {
        //         return false;

        //     };
        // };


        //-----------------------------------------------------------------------------------------------------------------------------------------------------------
        // INDEX  ---------------------------------------------------------------------------------------------------------------------------------------------------
        //-----------------------------------------------------------------------------------------------------------------------------------------------------------

        // Разрешено ли просматривать списки
        // if($method == 'index'){
        //     if(isset($session['all_rights'][$method . '-'. $entity_name .'-allow'])) {

        //         // Нет ли блокировки этого права?
        //         if(!isset($session['all_rights'][$method . '-'. $entity_name .'-deny'])) {

        //             //Разрешаем, так как блокировки нет!
        //             return true;

        //         } else {
        //             return false;
        //             // abort(403, 'Вам запрещено просматривать список!');
        //         };

        //     } else {
        //         return false;
        //         // abort(403, 'У вас нет прав на просмотр списка!');
        //     };
        // };

        // Пропускае бога на index
        if(($method == 'index')&&($user_status == 1)){return true;};



        if(($method == 'index')&&($user_status != 1)){

            // Получаем список филиалов в которых разрешено
            if(isset($session['all_rights'][$method . '-'. $entity_name .'-allow'])) {

                // Есть ли записи о филиалах
                if(isset($session['all_rights'][$method . '-'. $entity_name .'-allow']['filials'])) {

                    $mass_filials_allow = $session['all_rights'][$method . '-'. $entity_name .'-allow']['filials'];

                } else {
                    return false;
                    // abort(403, 'Вам запрещено просматривать список!');
                };

            } else {
                return false;
                // abort(403, 'У вас нет прав на просмотр списка!');
            };



            // Получаем список филиалов в которых разрешено
            if(isset($session['all_rights'][$method . '-'. $entity_name .'-deny'])) {

                // Есть ли записи о филиалах
                if(isset($session['all_rights'][$method . '-'. $entity_name .'-deny']['filials'])) {

                    $mass_filials_deny = $session['all_rights'][$method . '-'. $entity_name .'-deny']['filials'];

                } else {
                    return false;
                    // abort(403, 'Вам запрещено просматривать список!');
                };

            } else {
                return false;
                // abort(403, 'У вас нет прав на просмотр списка!');
            };



            $diff = collect($mass_filials_allow)->diff($mass_filials_deny)->toArray();

            // dd($diff);
            if(count($diff)>0){return true;} else{return false;};

        };


        //-----------------------------------------------------------------------------------------------------------------------------------------------------------
        // CREATE  --------------------------------------------------------------------------------------------------------------------------------------------------
        //-----------------------------------------------------------------------------------------------------------------------------------------------------------

        if($method == 'create'){
            if(isset($session['all_rights'][$method . '-'. $entity_name .'-allow'])) {

                // Нет ли блокировки этого права?
                if(!isset($session['all_rights'][$method . '-'. $entity_name .'-deny'])) {

                    //Разрешаем, так как блокировки нет!
                    return true;
                    
                } else {
                return false;
                    // abort(403, 'Вам запрещено создавать записи!');
                };

            } else {
                return false;
                // abort(403, 'У вас нет прав на создание записи');
            };
        };


        //-----------------------------------------------------------------------------------------------------------------------------------------------------------
        // UPDATE / VIEW / DELETE  ----------------------------------------------------------------------------------------------------------------------------------
        //-----------------------------------------------------------------------------------------------------------------------------------------------------------


        // Получаем статус ограничения по филиалам (Есть или нет)

        if(isset($session['all_rights']['nolimit-'. $entity_name .'-allow'])) {


            // Нет ли блокировки этого права?
            if(!isset($session['all_rights']['nolimit-'. $entity_name .'-deny'])) {
  
                //Разрешаем, так как блокировки нет!
                $nolimit_status = true;
                
            } else {$nolimit_status = false;};

        } else {
            $nolimit_status = false;
        };

        // Если сущность не зависит от филиалов, то переключаемся в режим 
        // независимости от филиалов.
        if($entity_dependence == false){
            $nolimit_status = true;
        };


        // Получаем статус наличия права в связке с филиалом (Есть или нет)
        if(($method == 'update')||($method == 'delete')||($method == 'view')||($method == 'moderator')||($method == 'automoderate')){

            if($nolimit_status == false){

                if(isset($session['all_rights'][$method . '-'. $entity_name .'-allow']['filials'][$model->filial_id])) {


                    // Нет ли блокировки этого права?
                    if(!isset($session['all_rights'][$method . '-'. $entity_name .'-deny']['filials'][$model->filial_id])) {

                        //Разрешаем, так как блокировки нет!
                        $right_dep_status = true;
                        
                    } else {
                        $right_dep_status = false;
                    };

                } else {
                    $right_dep_status = false;
                };
            };

            // Смотрим наличие права (и отсутствие запрета) вне зависимости от филиала.
            if(isset($session['all_rights'][$method . '-'. $entity_name .'-allow'])) {

                // Нет ли блокировки этого права?
                if(!isset($session['all_rights'][$method . '-'. $entity_name .'-deny'])) {

                    //Разрешаем, так как блокировки нет!
                    $right_status = true;
                    
                } else {
                    $right_status = false;
                };

            } else {
                $right_status = false;
            };


            // Главная проверка (учитывая настройки зависимостей)
            if((($right_status)&&($nolimit_status)) || $right_dep_status){
                $result = true;
            } else {
                $result = false;
                // abort(403, 'Вам не разрешена операция над этой записью');
            };

        };


        //-----------------------------------------------------------------------------------------------------------------------------------------------------------
        // SYSTEM ITEM ----------------------------------------------------------------------------------------------------------------------------------------------
        //-----------------------------------------------------------------------------------------------------------------------------------------------------------

        // Разрешения и запреты на операцию с системными записями
        if(isset($session['all_rights']['system-'. $entity_name .'-allow'])) {

            // Нет ли блокировки этого права?
            if(!isset($session['all_rights']['system-'. $entity_name .'-deny'])) {

                //Разрешаем, так как блокировки нет!
                $system_status = true;
                
            } else {$system_status = false;};

        } else {$system_status = false;};


        // Гасим любую операцию над системной записью без компании
        if(($model->system_item == 1)&&($model->company_id == null)&&($user_status == null)){
            return false;
        };

        // Проверка на возможность операций с системной записью
        if(($model->system_item == 1)&&($system_status == false)){
            return false;
        };

        // Получаем статус наличия права в связке с филиалом (Есть или нет)

        if(($method == 'delete')&&($model->system_item == 1)){
            return false;
            // abort(403, 'Удаление системных записей запрещено законом');
        };


        if(($method == 'update')&&($model->system_item == 1)&&($user_status != 1)){
            return false;
            // abort(403, 'Удаление системных записей запрещено законом');
        };

        //-----------------------------------------------------------------------------------------------------------------------------------------------------------
        // ПРОВЕРКА РАЗРЕШЕНИЙ ПО АВТОРАМ ---------------------------------------------------------------------------------------------------------------------------
        //-----------------------------------------------------------------------------------------------------------------------------------------------------------

        // Получаем статус наличия разрешения на чтение чужих записей (Есть или нет)

        if(isset($session['all_rights'][$method . '-'. $entity_name .'-allow'])) {

            // Нет ли блокировки этого права?
            if(!isset($session['all_rights'][$method . '-'. $entity_name .'-deny'])) {

                //Разрешаем, так как блокировки нет!
                $authors_status = true;
                
            } else {
                $authors_status = false;
                    // abort(403, 'Не прошли - 7! ;)');
            };

        } else {
            $authors_status = false;
            // abort(403, 'Не прошли - 8! ;)');
        };


        // По умолчанию (до проверки) мы не имеем права читать чужие записи
        $result_author = false;

        // Получаем список авторов
        if(isset($session['all_rights']['authors-'. $entity_name .'-allow']['authors']))
        {
            $list_authors = $session['all_rights']['authors-'. $entity_name .'-allow']['authors'];
        } else {
            $list_authors = null;
        };


        if(($authors_status)&&($nolimit_status)){

            if($list_authors == null){

                $result_author = true;

            } else {

                foreach($list_authors as $author){
                    
                    if($author == $model->author){
                        $result_author = true;
                    };

                };

                if($result_author == false){
                    // abort(403, 'Запись не относиться к авторам, записи которых вам разрешено редактировать!');
                };
            };

        // Если есть право читать чужие записи в режиме ОГРАНИЧЕНИЙ по филиалу
        } elseif(($authors_status)&&($nolimit_status == false)){



            // Вот тут нужно доработать -------------------------------------------
            if($list_authors == null){$result_author = true;}

            else {

                foreach($list_authors as $author){
                    if($author == $model->author){
                        $result_author = true;
                    };
                }

            };

            // Вот тут нужно доработать -------------------------------------------


        } else {
            // abort(403, "Ни каких прав не увидели по авторам. Ссорян! )");
            $result_author = false;
        };


        // Если запись это сам юзер - сразу даем зеленый свет! 
        if(($entity_name == 'users')&&($user_id == $model->id)){
            $result_author = true;
        };


            // dd($model->author_id);

        // Если запись своя - сразу даем зеленый свет!
        if($user_id == $model->author_id){

            $result_author = true;
        // Если есть право читать чужие записи в режиме БЕЗ ОГРАНИЧЕНИЙ по филиалу
        };

        //-----------------------------------------------------------------------------------------------------------------------------------------------------------
        // КОНЕЦ ПРОВЕРКИ РАЗРЕШЕНИЙ ПО АВТОРАМ ---------------------------------------------------------------------------------------------------------------------
        // ----------------------------------------------------------------------------------------------------------------------------------------------------------


        // Запрещаем операцию над богом
        if((isset($model->god))&&($model->god == 1)){
            $result = false;
            // abort(403, 'Доступ запрещен!!!');
        };


        if(($result_author)&&($result)){
            $result = true;
        } else {
            $result = false;
        };

        return $result;

    }
}
