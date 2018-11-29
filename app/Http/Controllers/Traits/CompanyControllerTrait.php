<?php

namespace App\Http\Controllers\Traits;
use App\Company;

// Транслитерация
use Transliterate;

trait CompanyControllerTrait
{
	public function createCompany($request){


		$company = Company::where('inn', $request->inn)->first();
		if(!isset($company)){

	        $company = new Company;
	        $last_id_company = Company::latest()->first()->id;
	        $number_id_company = $last_id_company + 1;

	        // Новые данные
	        $company->name = $request->company_name ?? $request->name;
	        $company->alias = $request->alias ?? Transliterate::make($company->name .'_'. $number_id_company, ['type' => 'url', 'lowercase' => true]);
	        $company->email = $request->email;
	        $company->legal_form_id = $request->legal_form_id;
	        $company->inn = $request->inn;
	        $company->kpp = $request->kpp;
	        $company->ogrn = $request->ogrn;
	        $company->okpo = $request->okpo;
	        $company->okved = $request->okved;
	        $company->location_id = create_location($request);
	        $company->sector_id = $request->sector_id;
	        $company->author_id = $request->user()->id;
	        $company->save();

	     }

        // Если запись удачна - будем записывать связи
        if($company){
        	
            add_phones($request, $company);
            addBankAccount($request, $company);
            setSchedule($request, $company);
            setServicesType($request, $company);

        } else {

            abort(403, 'Ошибка записи компании');
        };

        // dd($company);
        return $company;
    }


	public function updateCompany($request, $company){

       // Данные на обновление
       $company->name = $request->name;

        if ($company->alias != $request->alias) {
            $company->alias = $request->alias;
        }

        $company->email = $request->email;
        $company->legal_form_id = $request->legal_form_id;
        $company->inn = $request->inn;
        $company->kpp = $request->kpp;
        $company->ogrn = $request->ogrn;
        $company->okpo = $request->okpo;
        $company->okved = $request->okved;

        // Обновляем локацию
        $company = update_location($request, $company);

        if ($company->sector_id != $request->sector_id) {
            $company->sector_id = $request->sector_id;
        }

        $company->save();

        if($company){

            add_phones($request, $company);
            addBankAccount($request, $company);
            setSchedule($request, $company);
            setServicesType($request, $company);
        }
    }


}