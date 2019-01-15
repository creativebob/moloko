<?php

namespace App\Http\Controllers\Traits;
use App\Company;

// Транслитерация
use Transliterate;

trait CompanyControllerTrait
{
	public function createCompany($request)
    {


        $company = Company::where('inn', $request->inn)->whereNotNull('inn')->first();
        if(empty($company)){

            $company = new Company;
            if (Company::count() == 0) {
                $number_id_company = 1;
            } else {
                $last_id_company = Company::latest()->first()->id;
                $number_id_company = $last_id_company + 1;
            }


	        // Новые данные
            $company_name = $request->company_name ?? $request->name;

	        // Чистка имени компаниии от правовой формы и определения ID такой формы в базе
	        // Отдает массив с двумя переменными name и legal_form_id
            $result = cleanNameLegalForm($company_name);

	        // Если использовалась функция чистка имени - подставляем данные с нее
            $company->name = $result ? $result['name'] : $company_name;
            $company->legal_form_id = $result ? $result['legal_form_id'] : $request->legal_form_id;

            $company->alias = $request->alias ?? Transliterate::make($company->name .'_'. $number_id_company, ['type' => 'filename', 'lowercase' => true]);
            $company->email = $request->email;
            $company->inn = $request->inn;
            $company->kpp = $request->kpp;
            $company->ogrn = $request->ogrn;
            $company->okpo = $request->okpo;
            $company->okved = $request->okved;
            $company->location_id = create_location($request);
            $company->sector_id = $request->sector_id;
            $company->author_id = $request->user()->id;
            $company->external_control = $request->has('external_control');

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

        // Новые данные
        $company_name = $request->company_name ?? $request->name;

        // Чистка имени компаниии от правовой формы и определения ID такой формы в базе
        // Отдает массив с двумя переменными name и legal_form_id
        $result = cleanNameLegalForm($company_name);

        // Если использовалась функция чистка имени - подставляем данные с нее
        $company->name = $result ? $result['name'] : $company_name;
        $company->legal_form_id = $result ? $result['legal_form_id'] : $request->legal_form_id;

        if ($company->alias != $request->alias) {
            $company->alias = $request->alias;
        }

        $company->email = $request->email;
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

        $company->external_control = $request->has('external_control');
        $company->save();

        if($company){

            add_phones($request, $company);
            addBankAccount($request, $company);
            setSchedule($request, $company);
            setServicesType($request, $company);
        }
    }


}