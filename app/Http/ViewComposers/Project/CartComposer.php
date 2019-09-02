<?php

namespace App\Http\ViewComposers\Project;

use Illuminate\Support\Facades\Cookie;
use Illuminate\View\View;

class CartComposer
{
    public function compose(View $view)
    {
        if (isset($view->cart)) {
            $cart = $view->cart;
        } else {
            if (Cookie::has('cart')) {
                $cart = json_decode(Cookie::get('cart'), true);
            } else {
                $cart = [
                    'count' => 0,
                    'sum' => 0
                ];
            }
        }
//        dd($cart);

        return $view->with(compact('cart'));
    }

}