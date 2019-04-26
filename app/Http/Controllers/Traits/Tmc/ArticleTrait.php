<?php

namespace App\Http\Controllers\Traits\Tmc;

use App\Article;
use App\ArticlesGroup;
use App\Entity;

// Валидация
use App\Http\Requests\ArticleRequest;

trait ArticleTrait
{

    public function storeArticle(ArticleRequest $request, $category)
    {

        $user = $request->user();
        $user_id = $user->id;
        $company_id = $user->company_id;

        // Смотрим пришедший режим группы товаров
        switch ($request->mode) {

            case 'mode-default':
            $articles_group = ArticlesGroup::firstOrCreate([
                'name' => $request->name,
                'unit_id' => $request->unit_id,
            ], [
                'system_item' => $request->system_item ?? null,
                'display' => 1,
                'company_id' => $company_id,
                'author_id' => $user_id
            ]);

            // Пишем к группе связь с категорией
            $category->groups()->syncWithoutDetaching($articles_group->id);
            break;

            case 'mode-add':
            $articles_group = ArticlesGroup::firstOrCreate([
                'name' => $request->articles_group_name,
                'unit_id' => $request->unit_id,
            ], [
                'system_item' => $request->system_item ?? null,
                'display' => 1,
                'company_id' => $company_id,
                'author_id' => $user_id
            ]);

            // Пишем к группе связь с категорией
            $category->groups()->syncWithoutDetaching($articles_group->id);
            break;

            case 'mode-select':
            $articles_group = ArticlesGroup::findOrFail($request->articles_group_id);
            break;
        }

        $data = $request->input();
        $data['articles_group_id'] = $articles_group->id;

        $article = (new Article())->create($data);

        return $article;
    }


    public function updateArticle(ArticleRequest $request, $item)
    {

        $article = $item->article;
        // dd($article);

        // Получаем пришедшие данные
        $data = $request->input();
        $data['old_draft'] = $article->draft;
        // dd($data);

        // Проверка только если статус черновика не пришел, а сам артикул находится в черновике
        if (!request()->has('draft') && $data['old_draft'] == 1) {
            // Проверяем совпадение (отдаем пришедшие данные, т.к. мы не можем сейчас записать артикул, запись будет после проверки)
            // Придет либо массив с ошибками, либо null
            $result = $this->checkCoincidenceArticle($data);
            // dd($result);
        }
        // Проверки уже выведенного артикула

        // Если ошибок нет, то обновляем состав и сам артикул
        if (empty($result)) {

            $result = $this->checks($request, $item);
            // dd(__METHOD__, $result);


            if (is_array($result)) {
                return $result;
            } else {

                // Обновляем составы только для товаров
                if ($item->getTable() == 'goods') {
                    $this->setCompositions($article);
                }


                // Если ошибок и совпадений нет, то обновляем артикул
                $data['draft'] = request()->has('draft');
                $article->update($data);

                // Cохраняем / обновляем фото
                savePhoto($request, $article);

                return $article;
            }
        } else {
            // Если были ошибки, отдаем массив с ошибками
            return $result;
        }
    }

    protected function setCompositions(Article $article)
    {
        // ЗАпись состава только для черновика
        if ($article->draft) {
            $article->compositions()->sync(request()->compositions);
        }
    }

    // Проверяем артикул при выводе из черновика
    protected function checkCoincidenceArticle($data)
    {

        // dd($data);


        $articles = Article::where([
            'articles_group_id' => $data['articles_group_id'],
            'manufacturer_id' => $data['manufacturer_id'],
        ])
        ->where('draft', false)
        ->where('id', '!=', $data['id'])
        ->get([
            'id',
            'name',
            'articles_group_id',
            'manufacturer_id',
        ]);
        // dd($articles);

        // Если нашлись артикулы
        if ($articles->isNotEmpty()) {
            // Проверяем на наличие состава

            // Формируем массив пришедших составов артикула
            if (isset($data['compositions'])) {
                $article_compositions = [];
                foreach ($data['compositions'] as $id => $composition) {
                    $article_compositions[$id] = (int) $composition['value'];
                }
                // ksort($article_compositions);
                // dd($article_compositions);
                $articles = $articles->load('compositions');
            }

            // Проверяем значения составов
            foreach ($articles as $compared_article) {

                if (isset($data['compositions'])) {
                    if ($compared_article->compositions->isNotEmpty()) {
                        // Берем составы для первого найдденного артикула в группе
                        $compared_article_compositions = [];
                        foreach ($compared_article->compositions as $composition) {
                            $compared_article_compositions[$composition->id] = $composition->pivot->value;
                        }
                        // ksort($compared_article_compositions);
                        // dd($compared_article_compositions);
                    }
                }

                // Если составы и их значения совпали, то так как один производитель, даем ошибку
                if (isset($article_compositions) && isset($compared_article_compositions)) {
                    // dd('lol1');
                    if ($article_compositions == $compared_article_compositions) {
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
                    unset($compared_article_compositions);
                }
            }
            // dd('lol');
        }
    }

    // Проверки уже выведенного артикула
    protected function checks($request, $item)
    {

        $article = $item->article;

        // Проверка имени
        if ($article->name != $request->name) {

            $result = $this->checkName($request, $item);
            // dd($result_name);
            if (is_array($result)) {
                return $result;
            }
        }

        // Проверка смены группы
        if ($article->articles_group_id != $request->articles_group_id) {
            // dd(__METHOD__, $item->article, $request);
            $data = $request->input();
            // Так как производителя блокируем на шаблоне, то добавляем руками в массив
            $data['manufacturer_id'] = $item->article->manufacturer_id;

            // Если это товар и не шаблон, то вытаскиваем его состав для сравнения с артикулами из новой группы
            if ($item->getTable() == 'goods' && !$article->draft) {
                $article = $article->load('compositions');

                // ПРиводим массив к виду с шаблона
                $compositions = [];
                foreach ($article->compositions as $composition) {
                    $compositions[$composition->pivot->raw_id]['value'] = $composition->pivot->value;
                }
                // ksort($compositions);
                $data['compositions'] = $compositions;
            }

            $result = $this->checkCoincidenceArticle($data);

            if (is_array($result)) {
                return $result;
            }
        }
    }

    // Проверяем на совпадение имя артикула (не черновика)
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

    // Проверяем на совпадение имя артикула (не черновика)
    protected function changeCategory($request, $item)
    {

        // Получаем выбранную категорию со страницы (то, что указал пользователь)
        $category_id = $request->category_id;

            // Смотрим: была ли она изменена
        if ($item->category_id != $category_id) {

            $articles_group = $item->article->group;
            $category = $item->category;

                // Была изменена! Переназначаем категорию товару и группе:
            $category->groups()->detach($item->category_id);
            $category->groups()->attach($category_id);


            $entity = Entity::where('alias', $item->getTable())
            ->first(['model']);

            $model = 'App\\'.$entity->model;
            $items = $model::whereHas('article', function ($q) use ($articles_group) {
                $q->where('articles_group_id', $articles_group->id);
            })
            ->update([
                'category_id' => $category_id,
            ]);
        }

    }
}

