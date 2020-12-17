<?php

namespace App\Http\Controllers\System\Traits;

use App\Agent;
use App\Client;
use App\Company;
use App\Domain;
use App\Manufacturer;
use App\Site;
use App\Supplier;
use App\Vendor;

trait Companable
{
    /**
     * Store a newly created resource in storage.
     *
     * @return Company|\Illuminate\Database\Eloquent\Model|\Illuminate\Database\Query\Builder
     */
    public function storeCompany()
    {

        $request = request();

        $data = $request->input();
        $location = $this->getLocation();
        $data['location_id'] = $location->id;
//        dd($data);

        $company = Company::whereNotNull('inn')
            ->firstOrCreate([
                'inn' => $data['inn']
            ], $data);
//        dd($company);

        if ($company->wasRecentlyCreated) {
            logs('companies')->info("Создана компания. Id: [{$company->id}]");

            $photoId = $this->getPhotoId($company);
            $company->photo_id = $photoId;

            $names = [
                'black',
                'white',
                'color',
            ];
            foreach ($names as $name) {
                $column = $name . '_id';
                $company->$column = $this->saveVector($company, $name);
            }
            $company->save();

            $this->savePhones($company);

            if ($request->has('currencies')) {
                $company->currencies()->sync($request->currencies);
            }

            $company->settings()->sync($request->settings);

            addBankAccount($request, $company);
            setSchedule($request, $company);
            setProcessesType($request, $company);

            $this->getDomain($company);

        } else {
            logs('companies')->info("Компания существует. Id: [{$company->id}]");
        }

        if (!$company) {
            abort(403, __('errors.store'));
        };

        return $company;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param $company
     * @return Company|\Illuminate\Database\Eloquent\Model|\Illuminate\Database\Query\Builder
     */
    public function updateCompany($company)
    {
        $request = request();

//        $company = update_location($request, $company);

        $data = $request->input();

        $location = $this->getLocation();
        $data['location_id'] = $location->id;

        $photoId = $this->getPhotoId($company);
        $data['photo_id'] = $photoId;

        $names = [
            'black',
            'white',
            'color',
        ];

        foreach ($names as $name) {
            $column = $name . '_id';
            $data[$column] = $this->saveVector($company, $name);
        }

        $res = $company->update($data);

        if (!$res) {
            abort(403, __('errors.update'));
        }

        $this->savePhones($company);

        if ($request->has('currencies')) {
            $company->currencies()->sync($request->currencies);
        }

        $company->settings()->sync($request->settings);

        addBankAccount($request, $company);
        setSchedule($request, $company);
        setProcessesType($request, $company);

        $this->getDomain($company);

        // Логика создания на компанию связи: производитель / поставщик / и т.д.

//            $manufacturer = Manufacturer::where('company_id', $company->id)
//                ->where('manufacturer_id', $company->id)
//                ->first();
//
//            if(($manufacturer != null) && ($request->manufacturer_self != null)){
//
//                if($manufacturer->archive == 1){
//
//                    // Восстанавливаем связь из архива
//                    $manufacturer->archive = 0;
//                    $manufacturer->save();
//                }
//            }
//
//            if(($manufacturer == null) && ($request->manufacturer_self != null)){
//
//                // Создаем связь c нуля
//                $manufacturer = new Manufacturer;
//                $manufacturer->company_id = $auth_company_id;
//                $manufacturer->manufacturer_id = $company->id;
//
//                // Запись информации по производителю если нужно:
//                // ...
//
//                $manufacturer->save();
//
//                // dd("Записали себя производителем!");
//            }
//
//            $supplier = Supplier::where('company_id', $company->id)
//                ->where('supplier_id', $company->id)
//                ->first();
//
//            if(($supplier != null) && ($request->supplier_self != null)){
//
//                if($supplier->archive == 1){
//
//                    // Восстанавливаем связь из архива
//                    $supplier->archive = 0;
//                    $supplier->save();
//                }
//            }
//
//            if(($supplier == null) && ($request->supplier_self != null)){
//
//                // Создаем связь c нуля
//                $supplier = new Supplier;
//                $supplier->company_id = $auth_company_id;
//                $supplier->supplier_id = $company->id;
//
//                // Запись информации по производителю если нужно:
//                // ...
//
//                $supplier->save();
//
//                // dd("Записали себя поставщиком!");
//            }

//            if ($entity) {
//                // Проверка на обновление отношения
//                $vendor = Vendor::where('company_id', auth()->user()->company_id)
//                    ->where('supplier_id', $entity->id)
//                    ->first();
//
//                if ($request->is_vendor) {
//                    if ($vendor) {
//                        if ($vendor->archive) {
//                            // Восстанавливаем связь из архива
//                            $vendor->update([
//                                'archive' => false
//                            ]);
//                        }
//                    } else {
//                        $vendor = Vendor::create([
//                            'supplier_id' => $entity->id
//                        ]);
//                    }
//                } else {
//
//                    if ($vendor) {
//                        $vendor->update([
//                            'archive' => true
//                        ]);
//                    }
//                }
//            }


        logs('companies')->info("Обновлена компания. Id: [{$company->id}]");

        return $company;
    }

    /**
     * Устанавливаем статусы компании (поставщик / производитель)
     *
     * @param $company
     */
    public function setStatuses($company)
    {
        $request = request();

        if ($request->is_client == 1) {
            $client = Client::create([
                'clientable_id' => $company->id,
                'clientable_type' => 'App\Company',
            ]);
        }

        if ($request->is_vendor == 1) {
            $vendor = Vendor::create([
                'supplier_id' => $company->id
            ]);
        }

        if ($request->is_agent == 1) {
            $agent = Agent::create([
                'agent_id' => $company->id
            ]);
        }

        $supplier = null;
        if ($request->is_supplier == 1) {
            // TODO - 21.09.20 - Пока не можем добавить через отношение, т.к. оно организовано неверно
            $company->suppliers()->attach($company->id);
            $supplier = Supplier::create([
                'supplier_id' => $company->id
            ]);
        }

        $manufacturer = null;
        if ($request->is_manufacturer == 1) {
            $manufacturer = Manufacturer::create([
                'manufacturer_id' => $company->id
            ]);
        }

        if ($supplier && $manufacturer) {
            $supplier->manufacturers()->attach($manufacturer->id);
        }
    }

    public function checkCompanyByPhone($phone)
    {
        $company = Company::whereHas('main_phones', function ($q) use ($phone) {
                $q->where('phone', $phone);
            })
            ->first();
//        dd($company);

        return $company;
    }

    /**
     * Если пришел домен, добавляем его для компании. Если нет сайта, то создаем
     *
     * @param $company
     */
    public function getDomain($company)
    {
        // Проверяем домен
        if ($company->external_control == 0 && auth()->user()->company_id != $company->id) {
            $company->load([
                'site',
                'domain'
            ]);
            if (request()->get('domain')) {
                if (is_null($company->site)) {
                    $site = Site::firstOrcreate([
                        'name' => "Сайт {$company->id}",
                        'alias' => \Str::slug("Сайт {$company->id}"),
                        'company_id' => $company->id
                    ]);

                    $site->author_id = 1;
                    $site->editor_id = 1;
                    $site->company_id = $company->id;
                    $site->save();
                }

                if ($company->domain) {
                    if ($company->domain != request()->domain) {
                        $company->domain()->delete();
                    }
                }

                $company->load('site');
                $domain = Domain::firstOrCreate([
                    'domain' => request()->domain,
                    'site_id' => $company->site->id,
                    'company_id' => $company->id
                ]);

                $domain->author_id = 1;
                $domain->editor_id = 1;
                $domain->company_id = $company->id;
                $domain->save();

            } else {
                if ($company->site) {

                    if ($company->domain) {
                        $company->domain()->delete();
                    }
                }
            }
        }
    }
}
