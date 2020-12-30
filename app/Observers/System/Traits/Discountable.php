<?php

namespace App\Observers\System\Traits;

use App\CatalogsGoodsItem;
use App\CatalogsServicesItem;
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
            if ($priceGoods->id) {
                $priceGoods->load([
                    'discounts_actual'
                ]);
                if ($priceGoods->discounts_actual->first()) {
                    $discountPrice = $priceGoods->discounts_actual->first();
                    $priceGoods->price_discount_id = $discountPrice->id;
//                $discountPrice = Discount::find($priceGoods->discounts_actual->first()->id);
                    $resPriceDiscount = $this->getDynamicDiscount($discountPrice, $priceGoods->price);
                    $priceGoods->price_discount = $resPriceDiscount['amount'];
                    $priceGoods->total_price_discount = $priceGoods->price - $resPriceDiscount['amount'];
                    $break = $resPriceDiscount['break'];
                } else {
                    $priceGoods->price_discount_id = null;
                    $priceGoods->price_discount = 0;
                    $priceGoods->total_price_discount = $priceGoods->price;
                }
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

                $priceGoods->estimate_discount_id = null;
                $priceGoods->estimate_discount = 0;
                $priceGoods->total_estimate_discount = $priceGoods->total_catalogs_item_discount;
            } else {
                if ($priceGoods->id) {
                    $catalogsItem = $priceGoods->catalogs_item;
                } else {
                    $catalogsItem = CatalogsGoodsItem::find($priceGoods->catalogs_goods_item_id);
                }
                if ($catalogsItem->is_discount == 1) {
                        $catalogsItem->load([
                            'discounts_actual'
                        ]);

                    if ($catalogsItem->discounts_actual->first()) {
                        $discountCatalogsItem = $catalogsItem->discounts_actual->first();
                        $priceGoods->catalogs_item_discount_id = $discountCatalogsItem->id;
//                            $discountCatalogsItem = Discount::find($priceGoods->catalogs_item_discount_id);
                        $resCatalogsItemDiscount = $this->getDynamicDiscount($discountCatalogsItem, $priceGoods->total_price_discount);
                        $priceGoods->catalogs_item_discount = $resCatalogsItemDiscount['amount'];
                        $priceGoods->total_catalogs_item_discount = $priceGoods->total_price_discount - $resCatalogsItemDiscount['amount'];
                        $break = $resCatalogsItemDiscount['break'];
                    } else {
                        $priceGoods->catalogs_item_discount_id = null;
                        $priceGoods->catalogs_item_discount = 0;
                        $priceGoods->total_catalogs_item_discount = $priceGoods->total_price_discount;
                    }

                    if ($break) {
                        $priceGoods->estimate_discount_id = null;
                        $priceGoods->estimate_discount = 0;
                        $priceGoods->total_estimate_discount = $priceGoods->total_catalogs_item_discount;
                    } else {
                        $discountEstimate = Discount::where([
                            'company_id' => $priceGoods->company_id,
                            'archive' => false
                        ])
                            ->whereHas('entity', function ($q) {
                                $q->where('alias', 'estimates');
                            })
                            ->where('begined_at', '<=', now())
                            ->where(function ($q) {
                                $q->where('ended_at', '>', now())
                                    ->orWhereNull('ended_at');
                            })
                            ->first();
//                        dd($discountEstimate);

                        if ($discountEstimate) {
                            $priceGoods->estimate_discount_id = $discountEstimate->id;
                            $resEstimateDiscount = $this->getDynamicDiscount($discountEstimate, $priceGoods->total_catalogs_item_discount);
                            $priceGoods->estimate_discount = $resEstimateDiscount['amount'];
                            $priceGoods->total_estimate_discount = $priceGoods->total_catalogs_item_discount - $resEstimateDiscount['amount'];
//                            $break = $resEstimateDiscount['break'];
                        } else {
                            $priceGoods->estimate_discount_id = null;
                            $priceGoods->estimate_discount = 0;
                            $priceGoods->total_estimate_discount = $priceGoods->total_catalogs_item_discount;
                        }
                    }
                } else {
                    $priceGoods->catalogs_item_discount_id = null;
                    $priceGoods->catalogs_item_discount = 0;
                    $priceGoods->total_catalogs_item_discount = $priceGoods->total_price_discount;

                    $priceGoods->estimate_discount_id = null;
                    $priceGoods->estimate_discount = 0;
                    $priceGoods->total_estimate_discount = $priceGoods->total_catalogs_item_discount;
                }
            }
            $priceGoods->total = $priceGoods->total_estimate_discount;
//            dd($priceGoods);
        } else {
            $priceGoods->price_discount_id = null;
            $priceGoods->price_discount = 0;
            $priceGoods->total_price_discount = $priceGoods->price;

            $priceGoods->catalogs_item_discount_id = null;
            $priceGoods->catalogs_item_discount = 0;
            $priceGoods->total_catalogs_item_discount = $priceGoods->price;

            $priceGoods->estimate_discount_id = null;
            $priceGoods->estimate_discount = 0;
            $priceGoods->total_estimate_discount = $priceGoods->price;

            $priceGoods->total = $priceGoods->price;
        }
//        dd($priceGoods);
        return $priceGoods;
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

    public function setDiscountsPriceService($priceService)
    {
        if ($priceService->is_discount == 1) {
            $break = false;
            if ($priceService->id) {
                $priceService->load([
                    'discounts_actual'
                ]);
                if ($priceService->discounts_actual->first()) {
                    $discountPrice = $priceService->discounts_actual->first();
                    $priceService->price_discount_id = $discountPrice->id;
//                $discountPrice = Discount::find($priceService->discounts_actual->first()->id);
                    $resPriceDiscount = $this->getDynamicDiscount($discountPrice, $priceService->price);
                    $priceService->price_discount = $resPriceDiscount['amount'];
                    $priceService->total_price_discount = $priceService->price - $resPriceDiscount['amount'];
                    $break = $resPriceDiscount['break'];
                } else {
                    $priceService->price_discount_id = null;
                    $priceService->price_discount = 0;
                    $priceService->total_price_discount = $priceService->price;
                }
            } else {
                $priceService->price_discount_id = null;
                $priceService->price_discount = 0;
                $priceService->total_price_discount = $priceService->price;
            }
//            dd($break);

            if ($break) {
                $priceService->catalogs_item_discount_id = null;
                $priceService->catalogs_item_discount = 0;
                $priceService->total_catalogs_item_discount = $priceService->total_price_discount;

                $priceService->estimate_discount_id = null;
                $priceService->estimate_discount = 0;
                $priceService->total_estimate_discount = $priceService->total_catalogs_item_discount;
            } else {
                if ($priceService->id) {
                    $catalogsItem = $priceService->catalogs_item;
                } else {
                    $catalogsItem = CatalogsServicesItem::find($priceService->catalogs_services_item_id);
                }
                if ($catalogsItem->is_discount == 1) {
                    $catalogsItem->load([
                        'discounts_actual'
                    ]);

                    if ($catalogsItem->discounts_actual->first()) {
                        $discountCatalogsItem = $catalogsItem->discounts_actual->first();
                        $priceService->catalogs_item_discount_id = $discountCatalogsItem->id;
//                            $discountCatalogsItem = Discount::find($priceService->catalogs_item_discount_id);
                        $resCatalogsItemDiscount = $this->getDynamicDiscount($discountCatalogsItem, $priceService->total_price_discount);
                        $priceService->catalogs_item_discount = $resCatalogsItemDiscount['amount'];
                        $priceService->total_catalogs_item_discount = $priceService->total_price_discount - $resCatalogsItemDiscount['amount'];
                        $break = $resCatalogsItemDiscount['break'];
                    } else {
                        $priceService->catalogs_item_discount_id = null;
                        $priceService->catalogs_item_discount = 0;
                        $priceService->total_catalogs_item_discount = $priceService->total_price_discount;
                    }

                    if ($break) {
                        $priceService->estimate_discount_id = null;
                        $priceService->estimate_discount = 0;
                        $priceService->total_estimate_discount = $priceService->total_catalogs_item_discount;
                    } else {
                        $discountEstimate = Discount::where([
                            'company_id' => $priceService->company_id,
                            'archive' => false
                        ])
                            ->whereHas('entity', function ($q) {
                                $q->where('alias', 'estimates');
                            })
                            ->where('begined_at', '<=', now())
                            ->where(function ($q) {
                                $q->where('ended_at', '>', now())
                                    ->orWhereNull('ended_at');
                            })
                            ->first();
//                        dd($discountEstimate);

                        if ($discountEstimate) {
                            $priceService->estimate_discount_id = $discountEstimate->id;
                            $resEstimateDiscount = $this->getDynamicDiscount($discountEstimate, $priceService->total_catalogs_item_discount);
                            $priceService->estimate_discount = $resEstimateDiscount['amount'];
                            $priceService->total_estimate_discount = $priceService->total_catalogs_item_discount - $resEstimateDiscount['amount'];
//                            $break = $resEstimateDiscount['break'];
                        } else {
                            $priceService->estimate_discount_id = null;
                            $priceService->estimate_discount = 0;
                            $priceService->total_estimate_discount = $priceService->total_catalogs_item_discount;
                        }
                    }
                } else {
                    $priceService->catalogs_item_discount_id = null;
                    $priceService->catalogs_item_discount = 0;
                    $priceService->total_catalogs_item_discount = $priceService->total_price_discount;

                    $priceService->estimate_discount_id = null;
                    $priceService->estimate_discount = 0;
                    $priceService->total_estimate_discount = $priceService->total_catalogs_item_discount;
                }
            }
            $priceService->total = $priceService->total_estimate_discount;
//            dd($priceService);
        } else {
            $priceService->price_discount_id = null;
            $priceService->price_discount = 0;
            $priceService->total_price_discount = $priceService->price;

            $priceService->catalogs_item_discount_id = null;
            $priceService->catalogs_item_discount = 0;
            $priceService->total_catalogs_item_discount = $priceService->price;

            $priceService->estimate_discount_id = null;
            $priceService->estimate_discount = 0;
            $priceService->total_estimate_discount = $priceService->price;

            $priceService->total = $priceService->price;
        }
//        dd($priceService);
        return $priceService;
    }
}
