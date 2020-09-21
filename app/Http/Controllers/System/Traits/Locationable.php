<?php

namespace App\Http\Controllers\System\Traits;

use App\Location;

trait Locationable
{

    public function getLocation($countryId = 1, $cityId = 1, $address = null, $zipCode = null)
    {
        $request = request();

        // Ищем или создаем локацию
        $location = Location::with('city')
            ->firstOrCreate([
                'country_id' => $request->country_id ?? $countryId,
                'city_id' => $request->city_id ?? $cityId,
                'address' => $request->address ?? $address,
                'zip_code' => $request->zip_code ?? $zipCode
            ], [
                'author_id' => hideGod(auth()->user())
            ]);

        // TODO - 14.09.20 - В яндекс геокодере предположительно исчерпали квоту, пока нет парсера ширины / долготы
//        yandexGeocoder($location);
//        dd($location);

        return $location;
    }
}
