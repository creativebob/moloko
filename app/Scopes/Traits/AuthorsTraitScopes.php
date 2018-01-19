<?php

namespace App\Scopes\Traits;

trait AuthorsTraitScopes
{

        // Фильтрация для показа авторов
    public function scopeAuthors($query, $dependence, $session)
    {


      // ВКЛЮЧЕНИЕ ИЛИ ОТКЛЮЧЕНИЕ РАСШИРЯЮЩЕГО СПИСКА АВТОРОВ ------------------------------------------------------------------------------------

      // Добавление авторов пользователю. По умолчанию: true - добавить список авторов из сессии
      $use_authors = true;

      // Указываем - являеться ли сущность зависимой от филиала
      // false - независима / true - зависима

      // $dependence = true;

      if($dependence)
      {

          // Если выборка зависима
          // Проверяем в правах (которые записаны в сессию) наличие права на просмотр чужих записей 
          // и отсутствие такого запрета
          if(isset($session['all_rights']['authors-users-allow']) && (!isset($session['all_rights']['authors-users-deny'])))
          {

              // Фильтр не сработает если уйдет $authors = null и будут показаны все авторы компании без ограничений
              // Нам не важно, есть ли у него индивидуальные списки авторов.
              $authors = null;

              if($use_authors){
                 if(isset($session['all_rights']['authors-users-allow']['authors'])) {$authors = $session['all_rights']['authors-users-allow']['authors'];} else {$authors = null;};                            
              };


          } else {

              // Передаем список авторов из сессии в запрос
              
              $list_authors['authors_id'] = null;
              $authors = $list_authors;

          };

      } else {

              // Передаем список авторов из сессии в запрос
              if($use_authors){
                 if(isset($session['all_rights']['authors-users-allow']['authors'])) {$authors = $session['all_rights']['authors-users-allow']['authors'];} else {$authors = null;};                            
              };

      };

        if(isset($authors)){

          // Пробуем в модели получить сессию
          // $session  = session('access');
          // dd($list_authors = $session['list_authors']);

            if($authors['authors_id'] == null){

                // Получаем записи авторов которых нам открыли - получаем записи созданные нами - получаем себя
                return $query->Where('author_id', $authors['user_id'])->orWhere('id', $authors['user_id']);

            } else {

              // $authors['authors_id'] = collect($authors['authors_id'])->implode(', ');
              // // dd($authors['authors_id']);

                // // Получаем записи авторов которых нам открыли - получаем записи созданные нами - получаем себя
                return $query->WhereIn('author_id', $authors['authors_id'])->orWhere('author_id', $authors['user_id'])->orWhere('id', $authors['user_id']);

                // Получаем записи авторов которых нам открыли - получаем записи созданные нами - получаем себя

                // dd($filials);
                // return $query->whereHas('staff', function ($query) use ($filials){
                //   $query->whereIn('filial_id', $filials);
                // })->WhereIn('author_id', $authors['authors_id'])->orWhere('author_id', $authors['user_id'])->orWhere('id', $authors['user_id']);
            };

          } else {
  
              // Без ограничений
              // dd($authors['authors_id']);
               return $query;
          };
    }
}
