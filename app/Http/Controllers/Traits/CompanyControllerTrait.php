<?php

namespace App\Http\Controllers\Traits;

use App\Company;
use App\Manufacturer;
use App\Supplier;

// Транслитерация
// use Illuminate\Support\Str;
use Illuminate\Support\Str;

trait CompanyControllerTrait
{
	public function createCompany($request)
    {

        // Подготовка: -------------------------------------------------------------------------------------

        // Получаем данные для авторизованного пользователя
        $user_auth = $request->user();

        // Скрываем бога
        $user_auth_id = hideGod($user_auth);
        $auth_company_id = $user_auth->company_id;




        $company = Company::where('inn', $request->inn)->whereNotNull('inn')->first();
        if(empty($company)){

            $company = new Company;

            $company->name = $request->company_name ?? $request->name;

            if(isset($request->alias)){
                $company->alias = $request->alias;

            } else {

                // Вычисляем номер для использования в алиасе
                if (Company::count() == 0) {
                    $number_id_company = 1;
                } else {
                    $last_id_company = Company::latest()->first()->id;
                    $number_id_company = $last_id_company + 1;
                }

                $company->alias = Str::slug($company->name) .'-'. $number_id_company;

                // Если использовать нижнеее подчеркивание, то:
                // $company->alias = Transliterate::make($company->name .'_'. $number_id_company, ['type' => 'filename', 'lowercase' => true]);
            }

            $company->prename = $request->prename;
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
            $company->seo_description = $request->seo_description;
            $company->about = $request->about;

            $result = cleanNameLegalForm($request->company_name);
            $company->legal_form_id = $result ? $result['legal_form_id'] : $request->legal_form_id ?? 1;

            $company->save();

        }

        // Если запись удачна - будем записывать связи
        if($company){

            add_phones($request, $company);
            addBankAccount($request, $company);
            setSchedule($request, $company);
            setProcessesType($request, $company);

            // Если компания производит для себя, создадим ее связь с собой как с производителем
            if($request->manufacturer_self == 1){

                // Создаем связь
                $manufacturer = new Manufacturer;


                // Если компанию создает бог (без компании) и указывает, что она являеться производителем, 
                if($auth_company_id == null){
                    // то она будет производителем сама для себя
                    $manufacturer->company_id = $company->id;
                } else {
                    // в противном случае она будет производителем для компании под которой ее создают!
                    $manufacturer->company_id = $auth_company_id;
                }


                $manufacturer->manufacturer_id = $company->id;

                // Запись информации по производителю если нужно:
                // ...

                $manufacturer->save();
            }

            // Если компания производит для себя, создадим ее связь с собой как с производителем
            if($request->supplier_self == 1){

                // Создаем связь
                $supplier = new Supplier;
                $supplier->company_id = $auth_company_id;
                $supplier->supplier_id = $company->id;

                // Запись информации по производителю если нужно:
                // ...

                $supplier->save();
            }




        } else {

            abort(403, 'Ошибка записи компании');
        };


        return $company;
    }


    public function updateCompany($request, $company){


        // Подготовка: -------------------------------------------------------------------------------------

        // Получаем данные для авторизованного пользователя
        $user_auth = $request->user();

        // Скрываем бога
        $user_auth_id = hideGod($user_auth);
        $auth_company_id = $user_auth->company_id;


        // Новые данные
        $company_name = $request->company_name ?? $request->name;

        // dd($company_name);

        // Чистка имени компаниии от правовой формы и определения ID такой формы в базе
        // Отдает массив с двумя переменными name и legal_form_id
        $result = cleanNameLegalForm($company_name);

        // Если использовалась функция чистка имени - подставляем данные с нее
        if($result){$company->name = $result['name'];} else {$company->name = $company_name;};

        // dd($company->name);

        $company->legal_form_id = $result ? $result['legal_form_id'] : $request->legal_form_id;


        if ($company->alias != $request->alias) {
            $company->alias = $request->alias;
        }

        $company->prename = $request->prename;
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
        $company->seo_description = $request->seo_description;
        $company->about = $request->about;

        $company->save();

        if($company){

            add_phones($request, $company);
            addBankAccount($request, $company);
            setSchedule($request, $company);
            setProcessesType($request, $company);




            // Логика создания на компанию связи: производитель / поставщик / и т.д.

            $manufacturer = Manufacturer::where('company_id', $company->id)
            ->where('manufacturer_id', $company->id)
            ->first();

            if(($manufacturer != null) && ($request->manufacturer_self != null)){

                if($manufacturer->archive == 1){

                    // Восстанавливаем связь из архива
                    $manufacturer->archive = 0;
                    $manufacturer->save();
                }
            }

            if(($manufacturer == null) && ($request->manufacturer_self != null)){

                    // Создаем связь c нуля
                    $manufacturer = new Manufacturer;
                    $manufacturer->company_id = $auth_company_id;
                    $manufacturer->manufacturer_id = $company->id;

                    // Запись информации по производителю если нужно:
                    // ...

                    $manufacturer->save();

                    // dd("Записали себя производителем!");
            }

            $supplier = Supplier::where('company_id', $company->id)
            ->where('supplier_id', $company->id)
            ->first();

            if(($supplier != null) && ($request->supplier_self != null)){

                if($supplier->archive == 1){

                    // Восстанавливаем связь из архива
                    $supplier->archive = 0;
                    $supplier->save();
                }
            }

            if(($supplier == null) && ($request->supplier_self != null)){

                    // Создаем связь c нуля
                    $supplier = new Supplier;
                    $supplier->company_id = $auth_company_id;
                    $supplier->supplier_id = $company->id;

                    // Запись информации по производителю если нужно:
                    // ...

                    $supplier->save();

                    // dd("Записали себя поставщиком!");
            }




        }
    }


}
