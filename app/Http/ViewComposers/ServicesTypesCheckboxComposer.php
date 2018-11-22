<?php

namespace App\Http\ViewComposers;

use App\ServicesType;
use Illuminate\View\View;

class ServicesTypesCheckboxComposer
{
	public function compose(View $view)
	{

        $services_types = [];

        if($view->value != null){
          foreach ($view->value as $service_type){
              $services_types[] = $service_type->id;
          }
        }

        // Имя столбца
        $column = 'services_types_id';
        $request[$column] = $services_types;

        // dd($request[$column]);

        // Запрос для чекбокса - список типов услуг
        $services_types_query = ServicesType::get();

        // Контейнер для checkbox'а - инициируем
        $checkboxer['status'] = null;
        $checkboxer['entity_name'] = 'services_types';

        // Настраиваем checkboxer
        $services_types = addFilter(

            $checkboxer,                // Контейнер для checkbox'а
            $services_types_query,      // Коллекция которая будет взята
            $request,
            'Возможные типы услуг',     // Название чекбокса для пользователя в форме
            'services_types',           // Имя checkboxa для системы
            'id',                       // Поле записи которую ищем
            'services_types',
            'internal-self-one',        // Режим выборки через связи
            'checkboxer'                // Режим: checkboxer или filter

        );

		return $view->with('value', $services_types);

	}

}