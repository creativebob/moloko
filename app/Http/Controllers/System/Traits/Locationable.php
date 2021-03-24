<?php

namespace App\Http\Controllers\System\Traits;

use App\Location;
use App\City;
use Fomvasss\Dadata\Facades\DadataClean;

trait Locationable
{
    /**
     * Функция получения локации: либо через request (ничего передавать не нужно - из классических полей формы), 
     * либо через передачу параметров в функцию - для дополнительных адресов.
     * @param int $countryId
     * @param int $cityId
     * @param int $address
     * @param int $zipCode    
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model
     */

    public function getLocation($countryId = null, $cityId = null, $address = null, $zipCode = null)
    {

        // Если данные не были переданы явным способом (через список параметров), то получаем ВСЕ данные только из request,
        // в противном случае ВСЕ данные берем ТОЛЬКО из параметров которые передавали в функцию. Не смешиваем!
        if(($countryId == null) && ($cityId == null) && ($address == null) && ($zipCode == null)){
            
            $request = request();
            $countryId = $request->country_id;
            $cityId = $request->city_id;
            $address = $request->address;
            $zipCode = $request->zip_code;
        }

        // Далее, если не хватает данных о стране или городе, то берем их из системных умолчаний
        if($countryId == null) {$countryId = config('app.default_country_id');}
        if($cityId == null) {$cityId = config('app.default_city_id');}

        $latitude = null;
        $longitude = null;

        // Можем включить или выключить функцию определения
        // координат локации сервиса Dadata в config файле App

        if(config('app.dadata_parse_location')){
            $resultParse = $this->dadataParseLocation($cityId, $address);
            $latitude = $resultParse->geo_lat;
            $longitude = $resultParse->geo_lon;
        }

        // Ищем или создаем локацию
        $location = Location::with('city')
            ->firstOrCreate([
                'country_id' => $countryId,
                'city_id' => $cityId,
                'address' => $address,
            ], [
                'author_id' => hideGod(auth()->user()),
                'zip_code' => $zipCode,
                'latitude' => $latitude,
                'longitude' => $longitude,
            ]);


        // Смотрим - Это было создание новой записи (true) или получение уже созданной (false)?
        if($location->wasRecentlyCreated == false){

            // Если обнаружим отсутствие координат локации, делаем запрос
            // на парсинг и затем дописываем данные
            if(($location->latitude == null)||($location->longitude == null)){

                // Не хватает данных о координатах, будем парсить есть разрешено настройкой
                if(config('app.dadata_parse_location')){
                    $resultParse = $this->dadataParseLocation($cityId, $address);
                    $location->latitude = $resultParse->geo_lat;
                    $location->longitude = $resultParse->geo_lon;
                    $needSave = true;
                }
            }

            // Если видим, что у найденной локации не достает второстепенных данных (zip_code и могут быть другие)
            // или они отличаются от тех, что были записанны ранее - добавляем их на дозапись
            if($location->zip_code != $zipCode){
                $location->zip_code = $zipCode;
                $needSave = true;
            }

            // Если требуется запись - вписываем в модель
            if(isset($needSave)){
                $location->save();
            }
        }

        return $location;
    }



    /**
     * Получение координат локации с использованием сервиса DaData
     *
     * @param int $cityId
     * @param int $address
     * @return json
     */
    public function dadataParseLocation($cityId, $address)
    {
        $city = City::find($cityId);
        $resultParse = DadataClean::cleanAddress($city->name . ', ' . $address);

        return $resultParse;
    }


    // TODO - 14.09.20 - В яндекс геокодере предположительно исчерпали квоту, пока нет парсера ширины / долготы
    // Прошлое обращение: yandexGeocoder($location);


}
