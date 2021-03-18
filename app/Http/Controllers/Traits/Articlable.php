<?php

namespace App\Http\Controllers\Traits;

use App\Article;
use App\ArticlesGroup;
use App\Entity;
use App\Http\Controllers\System\Traits\Seoable;
use App\Http\Controllers\Traits\Photable;
use App\Http\Requests\System\ArticleUpdateRequest;
use App\Unit;

// Валидация
use App\Http\Requests\System\ArticleStoreRequest;

use Illuminate\Support\Facades\Log;

trait Articlable
{
    use Photable,
        Seoable;

    /**
     * Запись артикула в бд
     *
     * @param $request
     * @param $category
     * @return mixed
     */
    public function storeArticle($request, $category)
    {

        // TODO - 23.09.19 - При создании артикула не ищем похожую группу, а создаем новую (на Вкусняшке проблемы с дублированием имён)

        // Смотрим пришедший режим группы артикулов
        switch ($request->mode) {

            case 'mode-default':
//                $articles_group = ArticlesGroup::firstOrCreate([
//                    'name' => $request->name,
//                    'unit_id' => $request->unit_id,
//                    'units_category_id' => $request->units_category_id,
//                    'company_id' => $company_id,
//                ]);

                $data = $request->input();
                $articles_group = ArticlesGroup::create($data);

                // Пишем к группе связь с категорией
                $category->groups()->syncWithoutDetaching($articles_group->id);
            break;

            case 'mode-add':
//                $articles_group = ArticlesGroup::firstOrCreate([
//                    'name' => $request->group_name,
//                    'unit_id' => $request->unit_id,
//                    'units_category_id' => $request->units_category_id,
//                    'company_id' => $company_id
//                ]);

                $data = $request->input();
                $data['name'] = $request->group_name;
                $articles_group = ArticlesGroup::create($data);

                // Пишем к группе связь с категорией
                $category->groups()->syncWithoutDetaching($articles_group->id);
            break;

            case 'mode-select':
                $articles_group = ArticlesGroup::find($request->group_id);
            break;
        }

        Log::channel('operations')
        ->info('Режим создания: ' . $request->mode . '. Записали или нашли группу артикулов c id: ' . $articles_group->id . ', в зависимости от режима. Связали с категорией.');

        $data = $request->input();
        $data['articles_group_id'] = $articles_group->id;

        if (isset($data['units_category_id'])) {

            // Смотрим статичную категорию id 2 (Масса), если пришла она по переводим к выбранному коэффициенту
            if($data['units_category_id'] == 2) {

                $unit = Unit::find($data['unit_id']);
                $weight = $unit->ratio;
                $data['weight'] = $weight;

            } elseif ($data['units_category_id'] == 5) {

                $unit = Unit::find($data['unit_id']);
                $volume = $unit->ratio;
                $data['volume'] = $volume;

            } else {

                // Если нет, то умножаем пришедший вес на количество чего либо
                // $extra_unit = Unit::find($data['extra_unit_id']);


                // Если не пришло кол-во веса, значит у пользователя его не запросили, так как планируеться измерять
                // в единицах веса. Установим единицу!

                if(isset($data['weight'])){
                    $weight_unit = Unit::find($data['unit_weight_id']);
                    $weight = $data['weight'] * $weight_unit->ratio;
                    $data['weight'] = $weight;
                };

                if(isset($data['volume'])){
                    $volume_unit = Unit::find($data['unit_volume_id']);
                    $volume = $data['volume'] * $volume_unit->ratio;
                    $data['volume'] = $volume;
                };
            }
        }

        $article = Article::create($data);
        Log::channel('operations')
        ->info('Записали артикул с id: ' . $article->id);

        return $article;
    }

    /**
     * Изменение артикула в бд
     *
     * @param $request
     * @param $item
     * @return int|mixed
     */
    public function updateArticle($request, $item)
    {
        $article = $item->article;
        // dd($article);

        // Получаем пришедшие данные
        $data = $request->input();
        $data['old_draft'] = $article->draft;
        // dd($data);

        // Проверка только если статус черновика не пришел, а сам артикул находится в черновике
        if ((request()->draft == 0) && $data['old_draft'] == 1) {
            // Проверяем совпадение (отдаем пришедшие данные, т.к. мы не можем сейчас записать артикул, запись будет после проверки)
            // Придет либо массив с ошибками, либо null
            $result = $this->checkCoincidenceArticle($data);
        }
        // Проверки уже выведенного артикула

        // Если ошибок нет, то обновляем состав и сам артикул
        if (empty($result)) {

            $result = $this->checks($request, $item);

            if (is_array($result)) {
                return $result;
            } else {

                if ($article->draft) {
                    // Обновляем составы только для товаров в черновике
                    if ($item->getTable() == 'goods') {

                        if ($article->kit) {
                            $access = session('access.all_rights.index-goods-allow');
                            if ($access) {
                                $article->goods()->sync($request->goods);
                            }
                        } else {

                            $access = session('access.all_rights.index-raws-allow');
                            if ($access) {
                                $article->raws()->sync($request->raws);
                            }
                        }

                        $access = session('access.all_rights.index-containers-allow');
                        if ($access) {
                            $article->containers()->sync($request->containers);
                        }

                        $access = session('access.all_rights.index-attachments-allow');
                        if ($access) {
                            $article->attachments()->sync($request->attachments);
                        }
                    }

                    // Устаревший код
                    // if (isset($article->unit_id)) {
                    //     $unit = Unit::find($article->unit_id);
                    //     $weight = $data['weight'] * $unit->ratio;
                    //     $data['weight'] = $weight;
                    // }
                }

                $data['draft'] = $request->draft;
                // dd($data);

                $photo_id = $this->getPhotoId($article, $item->getTable());
                $data['photo_id'] = $photo_id;

                // Если ошибок и совпадений нет, то обновляем артикул
                $article->update($data);

                $article->parts()->sync($request->parts);

                $this->updateSeo($article);

                return $article;
            }
        } else {
            // Если были ошибки, отдаем массив с ошибками
            return $result;
        }
    }

    /**
     * Дублирование артикула
     *
     * @param $request
     * @param $item
     * @return mixed
     */
    public function replicateArticle($request, $item)
    {

        $article = $item->article;
        $new_article = $article->replicate();
        $new_article->name = $request->name;
        $new_article->draft = true;


        if ($request->cur_group == 0) {
            $group = $article->group;

            $data = $request->input();
            $data['unit_id'] = $group->unit_id;
            $data['units_category_id'] = $group->units_category_id;
            $articles_group = ArticlesGroup::create($data);

            // TODO - 23.09.19 - Изменения из за проблен на Вкусняшке
//            $user = $request->user();
//            $articles_group = ArticlesGroup::firstOrCreate([
//                'name' => $request->name,
//                'unit_id' => $group->unit_id,
//                'units_category_id' => $group->units_category_id,
//                'company_id' => $user->company_id,
//            ]);
            $new_article->articles_group_id = $articles_group->id;

            $category = $item->category;
            $category->groups()->syncWithoutDetaching($articles_group->id);
        }

        $new_article->photo_id = null;
        $new_article->album_id = null;

        $new_article->save();

        $new_article->update([
            'slug' => null
        ]);

        if ($new_article) {

            $photo_id = $this->replicatePhoto($article, $new_article);
            $new_article->photo_id = $photo_id;
//
            $album_id = $this->replicateAlbumWithPhotos($article, $new_article);
            $new_article->album_id = $album_id;

            $new_article->save();

            return $new_article;
        }
    }

    /**
     * Поиск артикула
     *
     * @param $search
     * @return \Illuminate\Http\JsonResponse
     */
    public function search($search)
    {

        // Подключение политики
//        $this->authorize('index',  $this->class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod('index'));

//        $search = $request->search;
        $items = $this->class::with([
            'article.manufacturer.company'
        ])
            ->moderatorLimit($answer)
            ->companiesLimit($answer)
            ->authors($answer)
            ->systemItem($answer) // Фильтр по системным записям
            ->whereHas('article', function ($q) use ($search) {
                $q->where('name', 'LIKE', '%' . $search . '%');
            })
            ->where('archive', false)
            ->get([
                'id',
                'article_id'
            ]);

//        dd($items);

        return response()->json($items);
    }

    /**
     * Проверяем артикул при выводе из черновика
     *
     * @param $data
     * @return mixed
     */
    protected function checkCoincidenceArticle($data)
    {
//         dd($data);

        $articles = Article::where([
            'articles_group_id' => $data['articles_group_id'],
            'manufacturer_id' => $data['manufacturer_id'],
        ])
        ->where('draft', false)
        ->where('id', '!=', $data['id'])
        ->get(
//            [
//            'id',
//            'name',
//            'articles_group_id',
//            'manufacturer_id',
//        ]
        );
        // dd($articles);

        // Если нашлись артикулы
        if ($articles->isNotEmpty()) {
            // Проверяем на наличие состава

            // Формируем массив пришедших составов артикула
            if (isset($data['raws'])) {
                $article_raws = [];
                foreach ($data['raws'] as $id => $raw) {
                    $article_raws[$id] = (int) $raw['value'];
                }
                // ksort($article_raws);
                // dd($article_raws);
                $articles = $articles->load('raws');
            }

            // Проверяем значения составов
            foreach ($articles as $compared_article) {

                if (isset($data['raws'])) {
                    if ($compared_article->raws->isNotEmpty()) {
                        // Берем составы для первого найдденного артикула в группе
                        $compared_article_raws = [];
                        foreach ($compared_article->raws as $raw) {
                            $compared_article_raws[$raw->id] = $raw->pivot->value;
                        }
                        // ksort($compared_article_raws);
                        // dd($compared_article_raws);
                    }
                }

                // Если составы и их значения совпали, то так как один производитель, даем ошибку
                if (isset($article_raws) && isset($compared_article_raws)) {
                    // dd('lol1');
                    if ($article_raws == $compared_article_raws) {
                        $result['msg'] = 'В данной групе существует артикул с таким составом и производителем.';
                        return $result;
                    } else {
                        // Если имя совпало даем ошибку
                        if ($data['name'] == $compared_article->name) {
                            $result['msg'] = 'В данной групе существует артикул с таким именем.';
                            return $result;
                        }
                    }
                } else {
                    // Если составы разные, смотрим имя, так как производитель один

                    // Если имя совпало даем ошибку
                    if ($data['name'] == $compared_article->name) {
                        $result['msg'] = 'В данной групе существует артикул с таким именем.';
                        return $result;
                    }

                    // Убиваем массив, чтоб создать новый
                    unset($compared_article_raws);
                }
            }
            // dd('lol');
        }
    }

    /**
     * Проверки уже выведенного артикула
     *
     * @param $request
     * @param $item
     * @return mixed
     */
    protected function checks($request, $item)
    {

        $article = $item->article;

        // Проверка имени
        if ($article->name != $request->name) {

            $result = $this->checkName($request, $item);
            if (is_array($result)) {
                return $result;
            }
        }

        // Проверка смены группы
        if ($article->articles_group_id != $request->articles_group_id) {
            $data = $request->input();
            // Так как производителя блокируем на шаблоне, то добавляем руками в массив
            $data['manufacturer_id'] = $item->article->manufacturer_id;

            // Если это товар и не шаблон, то вытаскиваем его состав для сравнения с артикулами из новой группы
            if ($item->getTable() == 'goods' && !$article->draft) {
                $article = $article->load('raws');

                // ПРиводим массив к виду с шаблона
                $raws = [];
                foreach ($article->raws as $raw) {
                    $raws[$raw->pivot->raw_id]['value'] = $raw->pivot->value;
                }
                // ksort($raws);
                $data['raws'] = $raws;
            }

            $result = $this->checkCoincidenceArticle($data);

            if (is_array($result)) {
                return $result;
            }
        }
    }

    /**
     * Проверяем на совпадение имени артикула (не черновика)
     *
     * @param $request
     * @param $item
     * @return mixed
     */
    protected function checkName($request, $item)
    {
        if (!$item->article->draft) {
            $article = $item->article;
            $articles_count = Article::where([
                'name' => $request->name,
                'articles_group_id' => $article->articles_group_id,
                'manufacturer_id' => $article->manufacturer_id,
            ])
            ->count();

            if ($articles_count > 0) {
                $result['msg'] = 'В данной групе существует артикул с таким именем.';
                return $result;
            }
        }
    }

    /**
     * Смена категории
     *
     * @param $request
     * @param $item
     */
    protected function changeCategory($request, $item)
    {
        // Получаем выбранную категорию со страницы (то, что указал пользователь)
        $category_id = $request->category_id;

        // Смотрим: была ли она изменена
        if ($item->category_id != $category_id) {

            $articles_group = $item->article->group;
            $category = $item->category;

            // Была изменена! Переназначаем категорию товару и группе:
            $category->groups()->detach($articles_group->id);


            $entity = Entity::where('alias', $item->category->getTable())
                ->first(['model']);
            $model = $entity->model;

            $new_category = $model::find($category_id);
            $new_category->groups()->attach($request->articles_group_id);

            $item->update([
                'category_id' => $category_id,
            ]);
        }
    }
}
