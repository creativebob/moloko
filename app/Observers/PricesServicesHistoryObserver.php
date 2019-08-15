<?php

namespace App\Observers;

use App\PricesServicesHistory;

use App\Observers\Traits\CommonTrait;
use Carbon\Carbon;

class PricesServicesHistoryObserver
{

    use CommonTrait;

    public function creating(PricesServicesHistory $prices_services_history)
    {
        $this->store($prices_services_history);
        $this->setBeginDate($prices_services_history);
    }

    public function updating(PricesServicesHistory $prices_services_history)
    {
        $this->update($prices_services_history);
    }

    public function deleting(PricesServicesHistory $prices_services_history)
    {
        $this->destroy($prices_services_history);
    }

    protected function setBeginDate($prices_services_history)
    {
        $prices_services_history->begin_date = Carbon::now();
    }

}
