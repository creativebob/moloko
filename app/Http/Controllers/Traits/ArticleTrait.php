<?php

namespace App\Http\Controllers\Traits;

use App\Article;
use App\ArticlesGroup;

trait ArticleTrait
{

	public function storeArticle($request, $category)
    {

        // dd($request->has('set_status'));
        $user = $request->user();
        $user_id = $user->id;
        $company_id = $user->company_id;

        // Смотрим пришедший режим группы товаров
        switch ($request->mode) {

            case 'mode-default':
            $articles_group = ArticlesGroup::firstOrCreate([
                'name' => $request->name,
                'set_status' => $request->has('set_status'),
            ], [
                'unit_id' => $request->unit_id,
                'system_item' => $request->system_item ? $request->system_item : null,
                'display' => 1,
                'company_id' => $company_id,
                'author_id' => $user_id
            ]);
            // Пишем к группе связь с категорией
            $relation = $category->getTable();
            $articles_group->$relation()->attach($category->id);
            break;

            case 'mode-add':
            $articles_group = ArticlesGroup::firstOrCreate([
                'name' => $request->articles_group_name,
                'set_status' => $request->has('set_status'),
            ], [
                'unit_id' => $request->unit_id,
                'system_item' => $request->system_item ? $request->system_item : null,
                'display' => 1,
                'company_id' => $company_id,
                'author_id' => $user_id
            ]);
            // Пишем к группе связь с категорией
            $relation = $category->getTable();
            $articles_group->$relation()->attach($category->id);
            break;

            case 'mode-select':
            $articles_group = ArticlesGroup::findOrFail($request->articles_group_id);
            break;
        }

        $article = new Article;
        $article->articles_group_id = $articles_group->id;
        $article->draft = 1;
        $article->company_id = $company_id;
        $article->author_id = $user_id;
        $article->name = $request->name;
        $article->price_default = $request->price_default;
        $article->save();

        return $article;
    }


    public function updateArticle($request, $item)
    {

        // Получаем из сессии необходимые данные (Функция находится в Helpers)
        $answer = operator_right('articles', false, 'update');

        // Получаем артикул товара
        $article = $item->article;
        // dd($item->goods_article->draft);

        // Проверки только для черновика
        if ($article->draft == 1) {

            // Определяем количество метрик и составов
            $metrics_count = isset($request->metrics) ? count($request->metrics) : 0;
            // dd($metrics_count);

            // Если пришли значения метрик
            // $metrics_values = [];
            // if (isset($request->metrics)) {
            //     // dd($request->metrics);

            //     // Получаем метрики, чтобы узнать их тип и знаки после запятой
            //     $keys = array_keys($request->metrics);
            //     // dd($keys);
            //     $metrics = Metric::with(['property' => function ($q) {
            //         $q->select('id', 'type');
            //     }])
            //     ->select('id', 'decimal_place', 'property_id')
            //     ->findOrFail($keys)
            //     ->keyBy('id');
            //     // dd($metrics);

            //     // Приводим значения в соответкствие
            //     foreach ($request->metrics as $metric_id => $values) {
            //         // dd($metrics[$metric_id]->decimal_place);
            //         if (($metrics[$metric_id]->property->type == 'numeric') || ($metrics[$metric_id]->property->type == 'percent')) {
            //             // dd(round($value[0] , $metrics[$metric_id]->decimal_place, PHP_ROUND_HALF_UP));
            //             if ($metrics[$metric_id]->decimal_place != 0) {
            //                 $metrics_values[$metric_id][] = round($values[0] , $metrics[$metric_id]->decimal_place, PHP_ROUND_HALF_UP);
            //             } else {
            //                 $metrics_values[$metric_id][] = (int)number_format($values[0], 0);
            //             }
            //         } else {
            //             $metrics_values[$metric_id] = $values;
            //         }
            //     }
            //     // dd($metrics_values);
            // }

            $compositions_count = isset($request->compositions_values) ? count($request->compositions_values) : 0;
            // dd($compositions_count);

            // Если пришли значения состава
            // $compositions_values = [];
            // if (isset($request->compositions_values)) {
            //     // dd($request->compositions_values);

            //     if ($item->article->product->set_status == 'one') {
            //         // Приводим значения в соответкствие
            //         foreach ($request->compositions_values as $composition_id => $value) {
            //             $compositions_values[$composition_id] = round($value , 2, PHP_ROUND_HALF_UP);
            //         }
            //     } else {
            //         foreach ($request->compositions_values as $composition_id => $value) {
            //             $compositions_values[$composition_id] = (int)number_format($value, 0);
            //         }
            //     }
            // }
            // dd($compositions_values);

            // Производитель
            $manufacturer_id = isset($request->manufacturer_id) ? $request->manufacturer_id : null;

            // если в черновике поменяли производителя
            if ($article->draft == 1) {
                if ($manufacturer_id != $article->manufacturer_id) {
                    $article = $item->article;
                    $article->manufacturer_id = $manufacturer_id;
                    $article->save();
                }
            }

            if ($article->name != $request->name) {
                $article->name = $request->name;
            }

            $article->manufacturer_id = $request->manufacturer_id;
            $article->metrics_count = $metrics_count;
            $article->compositions_count = $compositions_count;
            // $article->save();

            // Если нет прав на создание полноценной записи - запись отправляем на модерацию
            if ($answer['automoderate'] == false) {
                $item->moderation = 1;
            }

            // Метрики
            // if (count($metrics_values)) {

            //     $goods_article->metrics()->detach();

            //     $metrics_insert = [];
            //     // $metric->min = round($request->min , $request->decimal_place, PHP_ROUND_HALF_UP);
            //     foreach ($metrics_values as $metric_id => $values) {
            //         foreach ($values as $value) {
            //             // dd($value);
            //             $goods_article->metrics()->attach([
            //                 $metric_id => [
            //                     'value' => $value,
            //                 ]
            //             ]);
            //         }
            //     }
            //     // dd($metrics_insert);
            // } else {
            //     $article->metrics()->detach();
            // }

            // Состав
            // $compositions_relation = ($goods_article->product->set_status == 'one') ? 'compositions' : 'set_compositions';
            // if (count($compositions_values)) {

            //     $goods_article->$compositions_relation()->detach();

            //     $compositions_insert = [];
            //     foreach ($compositions_values as $composition_id => $value) {
            //         $compositions_insert[$composition_id] = [
            //             'value' => $value,
            //         ];
            //     }
            //     // dd($compositions_insert);
            //     $goods_article->$compositions_relation()->attach($compositions_insert);
            // } else {
            //     $goods_article->$compositions_relation()->detach();
            // }
        }

        $article->draft = $request->has('draft');

        // Если снят флаг черновика, проверяем на совпадение артикула
        // if (empty($request->draft) && $item->article->draft == 1) {

        //     // dd($request);

        //     $check_name = $this->check_coincidence_name($request);
        //     // dd($check_name);
        //     if ($check_name) {
        //         return redirect()->back()->withInput()->withErrors('Такой артикул уже существует других в группах');
        //     }

        //     $check_article = $this->check_coincidence_article($metrics_count, $metrics_values, $compositions_count, $compositions_values, $request->goods_product_id, $manufacturer_id);
        //     if ($check_article) {
        //         return redirect()->back()->withInput()->withErrors('Такой артикул уже существует в группе!');
        //     }

        //     $goods_article = $item->article;
        //     $goods_article->draft = null;
        //     $goods_article->save();
        //     // $goods_article = GoodsArticle::where('id', $item->goods_article_id)->update(['draft' => null]);
        // }

        // Если проверки пройдены, или меняем уже товар


        // -------------------------------------------------------------------------------------------------
        // ПЕРЕНОС ТОВАРА В ДРУГУЮ ГРУППУ ПОЛЬЗОВАТЕЛЕМ
        // Важно! Важно проверить, соответствеут ли группа в которую переноситься товар, метрикам самого товара
        // Если не соответствует - дать отказ. Если соответствует - осуществить перенос

        // Получаем выбранную группу со страницы (то, что указал пользователь)
        $articles_group_id = $request->articles_group_id;

        if ($article->articles_group_id != $articles_group_id ) {

            // Была изменена! Переназначаем группу артикулу:
            $article->articles_group_id = $articles_group_id;
        }

        // А, пока изменяем без проверки

        // Порции
        $article->portion_status = $request->portion_status;
        $article->portion_name = $request->portion_name;
        $article->portion_abbreviation = $request->portion_abbreviation;
        $article->portion_count = $request->portion_count;

        // Описание
        $article->description = $request->description;

        // Названия артикулов
        $article->manually = $request->manually;
        $article->external = $request->external;

        // Цены
        $article->cost_default = $request->cost_default;
        $article->price_default = $request->price_default;

        // Общие данные
        // $article->display = $request->display;
        // $article->system_item = $request->system_item;

        $article->editor_id = hideGod($request->user());
        $article->save();

        // Cохраняем / обновляем фото
        savePhoto($request, $article);

        return $article;
    }

}