<?php

namespace App\Observers\System;

use App\Discount;
use App\Observers\System\Traits\Discountable;
use App\Observers\System\Traits\Commonable;
use App\PricesGoods;

class PricesGoodsObserver
{

    use Commonable;
    use Discountable;

    public function creating(PricesGoods $priceGoods)
    {
        $this->store($priceGoods);
        $priceGoods->display = true;

        // TODO - 19.11.19 - Пока по дефолту рубль
        $priceGoods->currency_id = 1;
    
        $this->setDiscountsPriceGoods($priceGoods);
    }

    public function created(PricesGoods $priceGoods)
    {
        $priceGoods->history()->create([
            'price' => $priceGoods->price,
            'currency_id' => $priceGoods->currency_id,
        ]);
    }

    public function updating(PricesGoods $priceGoods)
    {
        $this->update($priceGoods);
        $this->setDiscountsPriceGoods($priceGoods);
    }

    public function deleting(PricesGoods $priceGoods)
    {
        $this->destroy($priceGoods);
    }

//    public function setTotal($priceGoods)
//    {
//        if ($priceGoods->is_discount == 1) {
//            $break = false;
//
//            if ($priceGoods->price_discount_id) {
//                $discountPrice = Discount::find($priceGoods->price_discount_id);
//                $resPriceDiscount = $this->getDynamicDiscount($discountPrice, $priceGoods->price);
//                $priceGoods->price_discount = $resPriceDiscount['amount'];
//                $priceGoods->total_price_discount = $priceGoods->price - $resPriceDiscount['amount'];
//                $break = $resPriceDiscount['break'];
//            } else {
//                $priceGoods->price_discount_id = null;
//                $priceGoods->price_discount = 0;
//                $priceGoods->total_price_discount = $priceGoods->price;
//            }
//
//            if ($break) {
//                $priceGoods->catalogs_item_discount_id = null;
//                $priceGoods->catalogs_item_discount = 0;
//                $priceGoods->total_catalogs_item_discount = $priceGoods->total_price_discount;
//            } else {
//                $priceGoods->load('catalogs_item');
//                $catalogsItem = $priceGoods->catalogs_item;
//                if ($catalogsItem->is_discount == 1) {
//                    if ($priceGoods->catalogs_item_discount_id) {
//                        $discountCatalogsItem = Discount::find($priceGoods->catalogs_item_discount_id);
//                        $resCatalogsItemDiscount = $this->getDynamicDiscount($discountCatalogsItem, $priceGoods->price);
//                        $priceGoods->catalogs_item_discount = $resCatalogsItemDiscount['amount'];
//                        $priceGoods->total_catalogs_item_discount = $priceGoods->total_price_discount - $resCatalogsItemDiscount['amount'];
//                    } else {
//                        $priceGoods->catalogs_item_discount_id = null;
//                        $priceGoods->catalogs_item_discount = 0;
//                        $priceGoods->total_catalogs_item_discount = $priceGoods->total_price_discount;
//                    }
//                } else {
//                    $priceGoods->catalogs_item_discount_id = null;
//                    $priceGoods->catalogs_item_discount = 0;
//                    $priceGoods->total_catalogs_item_discount = $priceGoods->total_price_discount;
//                }
//            }
//
//            $priceGoods->total = $priceGoods->total_catalogs_item_discount;
////            dd($priceGoods);
//        } else {
//            $priceGoods->price_discount_id = null;
//            $priceGoods->price_discount = 0;
//            $priceGoods->total_price_discount = $priceGoods->price;
//
//            $priceGoods->catalogs_item_discount_id = null;
//            $priceGoods->catalogs_item_discount = 0;
//            $priceGoods->total_catalogs_item_discount = $priceGoods->price;
//
//            $priceGoods->total = $priceGoods->price;
//        }
////        dd($priceGoods);
//
//        return $priceGoods;
//    }
}
