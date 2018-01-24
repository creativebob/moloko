<?php

namespace App\Policies\Traits;

trait PoliticTrait
{
        // Фильтрация для показа авторов
    public function getstatus($entity_name, $object)
    {
        // Получаем данные из сессии
        $session  = session('access');

        $user_id = $session['user_info']['user_id'];

        // Получаем данные объекта
        $system_item = $object->system_item;
        $id_author = $object->author;
        $object_id = $object->id;

        // Получаем статус ограничения по филиалам (Есть или нет)
        $nolimit_status = isset($session['all_rights']['nolimit-'. $entity_name .'-allow']);

        // Получаем статус наличия права в связке с филиалом (Есть или нет)
        $right_dep_status = isset($session['all_rights']['update-'. $entity_name .'-allow']['departments'][$object->filial_id]);

        // Получаем статус наличия общего права без связи с филиалом (Есть или нет)
        $right_status = isset($session['all_rights']['update-'. $entity_name .'-allow']);

        // Получаем статус наличия общего права без связи с филиалом (Есть или нет)
        $system_status = isset($session['all_rights']['system-'. $entity_name .'-allow']);

        // Получаем статус наличия разрешения на чтение чужих записей (Есть или нет)
        $authors_status = isset($session['all_rights']['system-'. $entity_name .'-allow']);

        // Главная проверка (учитывая настройки зависимостей)
        if((($right_status)&&($nolimit_status)) || $right_dep_status){$result = true;} else {
            $result = false; 
            abort(403, 'Вам не разрешена операция над этой записью');
        };

        // Проверка на возможность операций с системной записью
        if(($system_item == 1)&&($system_status == false)){
            $result = false;
            abort(403, 'Вам не разрешена операция над системной записью');
        };



        // ПРОВЕРКА РАЗРЕШЕНИЙ ПО АВТОРАМ ---------------------------------------------------------------------------------------------------------------------
        
        // По умолчанию (до проверки) мы не имеем права читать чужие записи
        $result_author = false;

        // Получаем список авторов
        if(isset($session['all_rights']['authors-'. $entity_name .'-allow']['authors']))
        {
            $list_authors = $session['all_rights']['authors-'. $entity_name .'-allow']['authors'];
        } else {
            $list_authors = null;
        };


        // Если запись своя - сразу даем зеленый свет!
        if($user_id == $id_author){

            $result_author = true;

        // Если есть право читать чужие записи в режиме БЕЗ ОГРАНИЧЕНИЙ по филиалу
        } elseif(($authors_status)&&($nolimit_status)){

            if($list_authors == null){$result_author = true;}

            else {
              foreach($list_authors as $author){
                if($author == $id_author){$result_author = true;};
                }              
            };

        // Если есть право читать чужие записи в режиме ОГРАНИЧЕНИЙ по филиалу
        } elseif(($authors_status)&&($nolimit_status == false)){



            // Вот тут нужно доработать -------------------------------------------
            if($list_authors == null){$result_author = true;}

            else {

                foreach($list_authors as $author){
                    if($author == $id_author){$result_author = true;};
                }

            };

            // Вот тут нужно доработать -------------------------------------------


        } else {
            $result_author = false;
        };


        // Если запись своя - сразу даем зеленый свет!
        if(($entity_name == 'users')&&($user_id == $object_id)){
            $result_author = true;
        };


        // КОНЕЦ ПРОВЕРКИ РАЗРЕШЕНИЙ ПО АВТОРАМ ---------------------------------------------------------------------------------------------------------------------

        if(($result_author)&&($result)){
            $result = true;
        } else {
            $result = false;
        };

        return $result;

    }
}
