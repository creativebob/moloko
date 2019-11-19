<?php

namespace App\Observers;

use App\PricesService;

use App\Observers\Traits\Commonable;

class PricesServiceObserver
{

    use Commonable;

    public function creating(PricesService $prices_service)
    {
        $this->store($prices_service);
        $prices_service->display = true;

        // TODO - 19.11.19 - Пока по дефолту рубль
        $prices_service->currency_id = 1;
    }

    public function created(PricesService $prices_service)
    {
        $prices_service->history()->create([
            'price' => $prices_service->price,
        ]);
    }

    public function updating(PricesService $prices_service)
    {
        $this->update($prices_service);
    }

    public function deleting(PricesService $prices_service)
    {
        $this->destroy($prices_service);
    }

}
