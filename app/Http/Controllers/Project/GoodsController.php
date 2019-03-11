<?php

namespace App\Http\Controllers\Project;

// Модели
use App\Goods;
use App\GoodsCategory;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class GoodsController extends Controller
{

    public function __construct(Request $request)
    {
        // parent::__construct();
        $this->page_alias = 'goods';
    }


    public function index($catalog_item_id = null)
    {

        dd($catalog_item_id);

        return view('project.goods.index', compact('company', 'page', 'alias', 'navigations', 'sidebar_goods', 'id', 'current_category'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($category_id, $id = null)
    {
       $navigations = $this->navigations();
        // dd($navigations);
        // dd($category_id);

        $company = $this->company();
        // dd($company);
        $alias = $this->page_alias;

        $page = pageProjectInfo($alias);

        if ($category_id == null) {
            $current_category = GoodsCategory::with('goods_products.goods')->has('goods_products', '>', 0)->first();

            if ($current_category) {
                $category_id = $current_category->id;
            }

        } else {
            $current_category = GoodsCategory::with('goods_products.goods')->findOrFail($category_id);
        }

        // dd($current_category);

        $goods_categories = GoodsCategory::where('company_id', 1)->get()->keyBy('id')->toArray();

        $goods_categories_list = [];
        $item = null;

        if (isset($goods_categories[$category_id]['parent_id'])) {
                // dd('lol');
            $goods_categories[$goods_categories[$category_id]['parent_id']]['item_id'] = $category_id;
            $item = $goods_categories[$category_id]['parent_id'];


            for ($i = 0; $i < count($goods_categories); $i++) {
                if (isset($goods_categories[$item]['parent_id'])) {
                    $item;
                    $goods_categories[$goods_categories[$item]['parent_id']]['item_id'] = $category_id;
                    $item = $goods_categories[$item]['parent_id'];

                }
            // dd($goods_categories);
            }
        }
        // dd($goods_categories);
        if ($goods_categories) {

            $sidebar_goods = get_parents_tree_with_item_id($goods_categories, $category_id);
        } else {
            $sidebar_goods = null;
        }

        $cur_goods = Goods::find($id);
        // dd($sidebar_goods);

        return view('project.goods.index', compact('company', 'page', 'alias', 'navigations', 'sidebar_goods', 'id', 'current_category', 'cur_goods'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
