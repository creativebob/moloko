<?php

namespace App\Observers\System;

use App\Article;
use App\Unit;

class ArticleObserver extends BaseObserver
{

    public function creating(Article $article)
    {
        $this->store($article);

        $article->draft = true;
        $request = request();


        if ($request->units_category_id == 2) {
            $article->unit_weight_id = $request->unit_id;
        }
        if ($request->units_category_id == 5) {
            $article->unit_volume_id = $request->unit_id;
        }

        if ($request->units_category_id == 32) {

            // Задаем умолчания
            $article->unit_weight_id = 8;   // кг.
            $article->unit_volume_id = 30;  // л.
        }
    }

    public function created(Article $article)
    {
        $slug = $this->getArticleSlug($article);
        $article->update([
            'slug' => $slug
        ]);
    }

    public function updating(Article $article)
    {
        $this->update($article);

        $request = request();
        // dd($request);

        // Проверки только для черновика
        if ($article->getOriginal('draft') == 1) {

            // Определяем количество метрик
            // $metrics_count = isset($request->metrics) ? count($request->metrics) : 0;
            // $article->metrics_count = $metrics_count;

            // // Определяем количество составов
            // $compositions_count = isset($request->compositions) ? count($request->compositions) : 0;
            // $article->compositions_count = $compositions_count;

        }


        // Работаем только если есть базовая единица (Исключает запуск при клонировании)
        if ($request->has('unit_id')) {

            // Ловим базовую единицу измерения
            if ($request->has('unit_id')) {
                $unit = Unit::find($request->unit_id);
            }

            // Может прийти вес
            if ($request->has('weight')) {
                $weight_unit = Unit::find($request->unit_weight_id);

                // dd('Базовый: ' . $unit->ratio . 'Специфичный: ' . $weight_unit->ratio . 'Начальный вес: ' . $request->weight);
                $weight = $request->weight * $weight_unit->ratio;
                $article->weight = $weight;
            };

            // Может прийти объем
            if ($request->has('volume')) {
                $volume_unit = Unit::find($request->unit_volume_id);
                $volume = $request->volume * $volume_unit->ratio;
                $article->volume = $volume;
            };

            // Если видим, что происходит смена единицы измерения
            if ($article->unit_id != $unit->id) {

                // Если работаем с мерой: ВЕС
                if ($unit->category_id == 2) {

                    $article->weight = $article->weight * $unit->ratio;
                    $article->unit_weight_id = $unit->id;
                }

                // Если работаем с мерой: ОБЪЕМ
                if ($unit->category_id == 5) {

                    $article->volume = $article->volume * $unit->ratio;
                    $article->unit_volume_id = $unit->id;
                }
            }


            // Порции
            if ($request->has('package_status')) {

                $article->package_status = $request->package_status;
                $article->package_name = $request->package_name;
                $article->package_abbreviation = $request->package_abbreviation;
                $article->package_count = $request->package_count;

            } else {

                $article->package_status = false;
            }
        }

        $slug = $this->getArticleSlug($article);
        $article->slug = $slug;

    }

    public function getArticleSlug(Article $article)
    {
        $slug = null;
        if (empty($article->slug)) {
            $slug = \Str::slug($article->name);

            $found = Article::where([
                'company_id' => $article->company_id,
                'slug' => $slug
            ])
                ->exists();

            if ($found) {
                $slug .= "-{$article->id}";
            }
        } else {
            $slug = $article->slug;
            $found = Article::where([
                'company_id' => $article->company_id,
                'slug' => $slug
            ])
                ->exists();

            if ($found) {
                $slug .= "-{$article->id}";
            }
        }
        return $slug;
    }
}
