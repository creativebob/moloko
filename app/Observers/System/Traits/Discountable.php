<?php

namespace App\Observers\System\Traits;

use App\CatalogsGoodsItem;
use App\Direction;
use App\Discount;

trait Discountable
{

    public function getDynamicDiscount($discount, $totalWithoutDiscounts)
    {
        $break = false;
        $amount = 0;
        switch ($discount->mode) {
            case(1):
                $amount = $totalWithoutDiscounts / 100 * $discount->percent;
                break;
            case(2):
                $amount = $discount->currency;
                break;
        }

        if ($discount->is_block == 1) {
            $break = true;
        }

        $result = [
            'amount' => $amount,
            'break' => $break
        ];

        return $result;
    }

    public function setDiscountsPriceGoods($priceGoods)
    {
        if ($priceGoods->is_discount == 1) {
            $break = false;

            if ($priceGoods->price_discount_id) {
                $discountPrice = Discount::find($priceGoods->price_discount_id);
                $resPriceDiscount = $this->getDynamicDiscount($discountPrice, $priceGoods->price);
                $priceGoods->price_discount = $resPriceDiscount['amount'];
                $priceGoods->total_price_discount = $priceGoods->price - $resPriceDiscount['amount'];
                $break = $resPriceDiscount['break'];
            } else {
                $priceGoods->price_discount_id = null;
                $priceGoods->price_discount = 0;
                $priceGoods->total_price_discount = $priceGoods->price;
            }
//            dd($break);

            if ($break) {
                $priceGoods->catalogs_item_discount_id = null;
                $priceGoods->catalogs_item_discount = 0;
                $priceGoods->total_catalogs_item_discount = $priceGoods->total_price_discount;
            } else {
                $catalogsItem = CatalogsGoodsItem::find($priceGoods->catalogs_goods_item_id);
                if ($catalogsItem->is_discount == 1) {
                    if ($priceGoods->catalogs_item_discount_id) {
                        $discountCatalogsItem = Discount::find($priceGoods->catalogs_item_discount_id);
                        $resCatalogsItemDiscount = $this->getDynamicDiscount($discountCatalogsItem, $priceGoods->price);
                        $priceGoods->catalogs_item_discount = $resCatalogsItemDiscount['amount'];
                        $priceGoods->total_catalogs_item_discount = $priceGoods->total_price_discount - $resCatalogsItemDiscount['amount'];
                    } else {
                        $priceGoods->catalogs_item_discount_id = null;
                        $priceGoods->catalogs_item_discount = 0;
                        $priceGoods->total_catalogs_item_discount = $priceGoods->total_price_discount;
                    }
                } else {
                    $priceGoods->catalogs_item_discount_id = null;
                    $priceGoods->catalogs_item_discount = 0;
                    $priceGoods->total_catalogs_item_discount = $priceGoods->total_price_discount;
                }
            }

            $priceGoods->total = $priceGoods->total_catalogs_item_discount;
//            dd($priceGoods);
        } else {
            $priceGoods->price_discount_id = null;
            $priceGoods->price_discount = 0;
            $priceGoods->total_price_discount = $priceGoods->price;

            $priceGoods->catalogs_item_discount_id = null;
            $priceGoods->catalogs_item_discount = 0;
            $priceGoods->total_catalogs_item_discount = $priceGoods->price;

            $priceGoods->total = $priceGoods->price;
        }
//        dd($priceGoods);
        return $priceGoods;
//        if ($priceGoods->is_discount == 1) {
//            $resPrice = $this->getDynamicDiscount($discount, $priceGoods->price);
//            $data['price_discount'] = $resPrice['amount'];
//            $data['total_price_discount'] = $priceGoods->price - $resPrice['amount'];
//
//            if (! $resPrice['break']) {
//                $catalogsGoodsItem = $priceGoods->catalogs_item;
//                if ($catalogsGoodsItem->is_discount == 1) {
//                    if ($catalogsGoodsItem->discounts_actual->isNotEmpty()) {
//                        $discountCatalogsItem = $catalogsGoodsItem->discounts_actual->first();
////                        dd($discountCatalogsItem);
//                        $resCatalogItem = $this->getDynamicDiscount($discountCatalogsItem, $priceGoods->price);
//                        $data['catalogs_item_discount'] = $resCatalogItem['amount'];
//                        $data['total_catalogs_item_discount'] = $data['total_price_discount'] - $resCatalogItem['amount'];
//                    }
//                } else {
//                    $data['catalogs_item_discount_id'] = null;
//                    $data['catalogs_item_discount'] = 0;
//                    $data['total_catalogs_item_discount'] = $data['total_price_discount'];
//                }
//            }
//
//        } else {
//            $data['price_discount_id'] = null;
//            $data['price_discount'] = 0;
//            $data['total_price_discount'] = $priceGoods->price;
//            $data['catalogs_item_discount_id'] = null;
//            $data['catalogs_item_discount'] = 0;
//            $data['total_catalogs_item_discount'] = $priceGoods->price;
//        }
//        dd($data);
//        $priceGoods->update($data);
    }

    public function updateDiscountCatalogsGoodsItem($catalogsGoodsItem, $discount)
    {

        $discountCatalogsItem = $discount;
        foreach ($catalogsGoodsItem->prices_goods_actual as $priceGoods) {

            if ($priceGoods->is_discount == 1) {
                $resPrice = $this->getDynamicDiscount($priceGoods->discounts_actual->first(), $priceGoods->price);
                $data['price_discount'] = $resPrice['amount'];

                if (! $resPrice['break']) {
                    if ($catalogsGoodsItem->is_discount == 1) {
                        $resCatalogItem = $this->getDynamicDiscount($discount, $priceGoods->price);
                        $data['catalogs_item_discount'] = $resCatalogItem['amount'];
                    } else {
                        $data['catalogs_item_discount_id'] = null;
                        $data['catalogs_item_discount'] = 0;
                    }
                }

            } else {
                $data['price_discount_id'] = null;
                $data['price_discount'] = 0;
            }
//            dd($data);
            $priceGoods->update($data);


        }

    }
}
