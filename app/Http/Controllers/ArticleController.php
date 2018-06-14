<?php

namespace App\Http\Controllers;

// Модели
use App\Article;
use App\Product;


use Illuminate\Http\Request;

class ArticleController extends Controller
{

    // Сущность над которой производит операции контроллер
    protected $entity_name = 'articles';
    protected $entity_dependence = false;

    public function index()
    {
        //
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

        // Подключение политики
        // $this->authorize(getmethod(__FUNCTION__), Article::class);

        $product_id = $request->product_id;

        // Вытаскиваем продукт
        $product = Product::with('metrics', 'compositions')->findOrFail($product_id);

        $metrics = [];
        $metrics_list = [];
        $metrics_values = [];
        foreach ($product->metrics as $metric) {

            $input = 'metrics-'.$metric->id;

            $metrics_list[] = $metric->id;

            $metrics_values[$metric->id] = $request->$input;

            $metrics[$metric->id] = [
                'entity' => 'metrics',
                'value' => $request->$input,
            ];
        }
        $metrics_count = count($metrics);

        // dd($metrics_list);

        $compositions = [];
        $compositions_list = [];
        foreach ($product->compositions as $composition) {

            $input = 'compositions-'.$composition->id;

            $compositions_list[] = $composition->id;

            $compositions[$composition->id] = [
                'entity' => 'compositions',
                'value' => $request->$input,
            ];
        }

         // dd($compositions_list);

        $compositions_count = count($compositions);

        // Проверка на наличие артикула
        $articles = Article::where('product_id', $product_id)
        ->where(['metrics_count' => $metrics_count, 'compositions_count' => $compositions_count])
        ->get();

        // dd($articles);

        $array = [];
        foreach ($articles as $article) {
            foreach ($article->metrics as $metric) {
                $array[$article->id][$metric->id] = $metric->pivot->value;
            }
        }

        // dd($array);

        // dd($metrics_values);

        foreach ($array as $item) {

            $a = collect($item)->diffAssoc($metrics_values);

            if (!isset($a)) {
                dd('lol');
            }
           
        }

        // dd($a->all());

        



        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        // Смотрим компанию пользователя
        $company_id = $user->company_id;

        // Скрываем бога
        $user_id = hideGod($user);

        // Наполняем сущность данными

        $article = new Article;
        $article->product_id = $request->product_id;
        $article->name = $request->name;
        $article->external = $request->external;
        $article->cost = $request->cost;
        $article->price = $request->price;

        $article->metrics_count = $metrics_count;
        $article->compositions_count = $compositions_count;

        // Если нет прав на создание полноценной записи - запись отправляем на модерацию
        if ($answer['automoderate'] == false) {
            $article->moderation = 1;
        }

        // Системная запись
        $article->system_item = $request->system_item;

        $article->company_id = $company_id;
        $article->author_id = $user_id;
        $article->save();

        if ($article) {



            // Пишем метрики
            $article->metrics()->attach($metrics);

            // Пишем состав
            $article->compositions()->attach($compositions);

            return view('products.article', compact('article'));


        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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

    public function get_article_inputs(Request $request)
    {

        $product = Product::with('metrics.property', 'compositions.unit')->findOrFail($request->product_id);
        return view('products.article-form', compact('product'));

         // $product = Product::with('metrics.property', 'compositions.unit')->findOrFail(1);
        // dd($product);


    }
}
