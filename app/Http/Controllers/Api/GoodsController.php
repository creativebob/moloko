<?php

namespace App\Http\Controllers\Api;

use App\Goods;

use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GoodsController extends Controller
{

    public function __construct()
    {

        // $this->middleware('auth');

        // Сущность над которой производит операции контроллер
        // $this->entity_name = 'goods';
        // $this->entity_dependence = false;
    }

    // Проверка совпадения артикула
    public function checkArticle(Request $request)
    {

        $goods_count = Goods::where(['manually' => $request->value, 'company_id' => $request->company_id])
        ->where('id', '!=', $request->id)
        ->count();
        return response()->json($goods_count);
    }
}
