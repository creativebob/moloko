<?php

namespace App\Observers;

use App\PricesService;

use App\Observers\Traits\CommonTrait;

class PricesServiceObserver
{

    use CommonTrait;

    public function creating(PricesService $prices_service)
    {
        $this->store($prices_service);
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
