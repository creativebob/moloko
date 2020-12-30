<?php

namespace App\Observers\System;

use App\Observers\System\Traits\Discountable;
use App\PricesService;

class PricesServiceObserver extends BaseObserver
{
    use Discountable;

    /**
     * Handle the priceService "creating" event.
     *
     * @param PricesService $priceService
     */
    public function creating(PricesService $priceService)
    {
        $this->store($priceService);
        $priceService->display = true;

        // TODO - 19.11.19 - Пока по дефолту рубль
        $priceService->currency_id = 1;

        $this->setDiscountsPriceService($priceService);
    }

    /**
     * Handle the priceService "created" event.
     *
     * @param PricesService $priceService
     */
    public function created(PricesService $priceService)
    {
        $priceService->history()->create([
            'price' => $priceService->price,
            'currency_id' => $priceService->currency_id,
        ]);

        $priceService->sort = $priceService->id;
        $priceService->save();
    }

    /**
     * Handle the priceService "updating" event.
     *
     * @param PricesService $priceService
     */
    public function updating(PricesService $priceService)
    {
        $this->update($priceService);
        $this->setDiscountsPriceService($priceService);
        $priceService->is_need_recalculate = false;
    }

    /**
     * Handle the priceService "deleting" event.
     *
     * @param PricesService $priceService
     */
    public function deleting(PricesService $priceService)
    {
        $this->destroy($priceService);
    }

}
