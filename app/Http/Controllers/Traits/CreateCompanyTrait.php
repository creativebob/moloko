<?php

namespace App\Http\Controllers\Traits;

trait CreateCompanyTrait
{

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
            setServicesType();

        } else {

            abort(403, 'Ошибка записи компании');
        };
}