<?php

namespace App\Http\Controllers\Traits;

use App\Manufacturer;

trait CreateCompanyTrait
{

        dd('Тут же я!');

        // Новые данные
        $company->name = $request->name;
        $company->alias = $request->alias;
        $company->email = $request->email;
        $company->legal_form_id = $request->legal_form_id;
        $company->inn = $request->inn;
        $company->kpp = $request->kpp;
        $company->ogrn = $request->ogrn;
        $company->okpo = $request->okpo;
        $company->okved = $request->okved;
        $company->location_id = create_location($request);
        $company->sector_id = $request->sector_id;
        $company->author_id = $user_id;
        $company->save();

        // Если запись удачна - будем записывать связи
        if($company){

            add_phones($request, $company);
            addBankAccount($company, $request);
            setSchedule($company, $request);
            setProcessesType();

            // Если компания производит для себя, создадим ее связь с собой как с производителем
            if($request->manufacturer_self == 1){

                // Создаем связь
                $manufacturer = new Manufacturer;
                $manufacturer->company_id = $company->id;
                $manufacturer->manufacturer_id = $company->id;

                // Запись информации по производителю если нужно:
                // ...

                $manufacturer->save();

            }

        } else {

            abort(403, 'Ошибка записи компании');
        };
}