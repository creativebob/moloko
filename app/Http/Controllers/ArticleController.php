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

    public function create()
    {
        //
    }

    public function store(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Article::class);

        $product_id = $request->product_id;

        // Вытаскиваем продукт
        $product = Product::with('metrics', 'compositions')->findOrFail($product_id);
        // dd($product);

        // Формируем массивы метрик
        $metrics = [];
        $metrics_values = [];

        foreach ($product->metrics as $metric) {

            $input = 'metrics-'.$metric->id;

            $metrics_values[$metric->id] = $request->$input;

            $metrics[$metric->id] = [
                'entity' => 'metrics',
                'value' => $request->$input,
            ];
        }
        $metrics_count = count($metrics);

        // dd($metrics_values);

        // Формируем массивы составов
        $compositions = [];
        $compositions_values = [];

        foreach ($product->compositions as $composition) {

            $input = 'compositions-'.$composition->id;

            $compositions_values[$composition->id] = $request->$input;

            $compositions[$composition->id] = [
                'entity' => 'compositions',
                'value' => $request->$input,
            ];
        }
        $compositions_count = count($compositions);
        // dd($compositions_values);

        // Проверка на наличие артикула

        // Вытаскиваем артикулы продукции с нужным нам числом метрик и составов
        $articles = Article::with('metrics', 'compositions')
        ->where('product_id', $product_id)
        ->where(['metrics_count' => $metrics_count, 'compositions_count' => $compositions_count])
        ->get();
        // dd($articles);

        // Создаем массив совпадений
        $coincidence = [];

        // Сравниваем метрики
        $metrics_array = [];
        foreach ($articles as $article) {
            foreach ($article->metrics as $metric) {
                $metrics_array[$article->id][$metric->id] = $metric->pivot->value;
            }
        }
        // dd($metrics_array);
        // dd($metrics_values);

        foreach ($metrics_array as $item) {
            if ($metrics_values == $item) {

                // Если значения метрик совпали, создаюм ключ метрик
                $coincidence['metric'] = 1;
            }
        }

        // Сравниваем составы
        $compositions_array = [];
        foreach ($articles as $article) {
            foreach ($article->compositions as $composition) {
                $compositions_array[$article->id][$composition->id] = $composition->pivot->value;
            }
        }
        // dd($compositions_array);
        // dd($compositions_values);

        foreach ($compositions_array as $item) {
            if ($compositions_values == $item) {

                // Если значения составов совпали, создаюм ключ составов
                $coincidence['composition'] = 1;
            }
        }

        // Проверяем наличие ключей в массиве
        if ((array_key_exists('metric', $coincidence)&&array_key_exists('composition', $coincidence))||(array_key_exists('metric', $coincidence)&&$product->compositions)||(array_key_exists('composition', $coincidence)&&$product->metrics)) {

            // Если ключи присутствуют, даем ошибку
            $result = [
                'error_status' => 1,
                'error_message' => 'Такой артикул уже существует!',
            ];

            echo json_encode($result, JSON_UNESCAPED_UNICODE);
        } else {

            // Если что то не совпало, пишем новый артикул

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
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }

    public function get_article_inputs(Request $request)
    {

        $product = Product::with('metrics.property', 'compositions.unit')->withCount('metrics', 'compositions')->findOrFail($request->product_id);
        return view('products.article-form', compact('product'));

         // $product = Product::with('metrics.property', 'compositions.unit')->findOrFail(1);
        // dd($product);


    }
}
