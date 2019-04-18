<?php

namespace App\Http\Controllers\Traits\Articles;

use App\Article;
use App\ArticlesGroup;

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

        $data = $request->input();
        $data['articles_group_id'] = $articles_group->id;

        $article = (new Article())->create($data);

        $compositions = $category->compositions->pluck('id')->toArray();
        $article->compositions()->sync($compositions);

        return $article;
    }


    public function updateArticle(ArticleRequest $request, $article)
    {

        // Получаем пришедшие данные
        $data = $request->input();
        $data['old_draft'] = $article->draft;

        // Проверяем совпадение (отдаем пришедшие данные, т.к. мы не можем сейчас записать артикул, запись будет после проверки)
        // Придет либо массив с ошибками, либо null
        $result = $this->getCoincidenceArticle($data);
        // dd($result);

        // Если ошибок нет, то обновляем состав и сам артикул
        if (empty($result)) {

            // -------------------------------------------------------------------------------------------------
            // TODO: ПЕРЕНОС ТОВАРА В ДРУГУЮ ГРУППУ ПОЛЬЗОВАТЕЛЕМ
            // Важно! Важно проверить, соответствеут ли группа в которую переноситься товар, метрикам самого товара
            // Если не соответствует - дать отказ. Если соответствует - осуществить перенос

            // Получаем выбранную группу со страницы (то, что указал пользователь)
            // $articles_group_id = $request->articles_group_id;
            // if ($article->articles_group_id != $articles_group_id ) {
            //     // Была изменена! Переназначаем группу артикулу:
            //     $article->articles_group_id = $articles_group_id;
            // }
            // А, пока изменяем без проверки

            // Обновляем составы
            $this->setCompositions($article);

            // Если ошибок и совпадений нет, то обновляем артикул
            $data['draft'] = request()->has('draft');
            $article->update($data);

            // Cохраняем / обновляем фото
            savePhoto($request, $article);

            return $article;
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

    protected function getCoincidenceArticle($data)
    {

        // Проверка только если статус черновика не пришел, а сам артикул находится в черновике
        if (!request()->has('draft') && $data['old_draft'] == 1) {

            $compositions_count = isset($data['compositions']) ? count($data['compositions']) : 0;

            $articles = Article::with([
                'compositions',
            ])
            ->where([
                'articles_group_id' => $data['articles_group_id'],
                'compositions_count' => $compositions_count,
                'manufacturer_id' => $data['manufacturer_id'],
            ])
            ->where('id', '!=', $data['id'])
            ->get([
                'id',
                'name',
                'articles_group_id',
                'compositions_count',
                'manufacturer_id',
            ]);
            // dd($articles);

            // Если нашлись артикулы
            if ($articles->isNotEmpty()) {

                // Проверяем на наличие состава
                if ($compositions_count > 0) {

                    // Формируем массив пришедших составов артикула
                    $article_compositions = [];
                    foreach ($data['compositions'] as $id => $composition) {
                        $article_compositions[$id] = (int) $composition['value'];
                    }

                    // Проверяем значения составов
                    foreach ($articles as $compared_article) {
                        // Берем составы для первого найдденного артикула в группе
                        $compared_article_compositions = [];

                        foreach ($compared_article->compositions as $composition) {
                            $compared_article_compositions[$composition->id] = $composition->pivot->value;
                        }

                        // Если составы и их значения совпали, проверяем произодителя
                        if ($article_compositions == $compared_article_compositions) {

                            // Если производители совпали, даем ошибку
                            if ($data['manufacturer_id'] == $compared_article->manufacturer_id) {
                                $result['msg'] = 'В данной групе существуют артикулы с аналогичным составом и производителем.';
                                return $result;
                            }
                        } else {
                            // Если составы разные, смотрим производителя

                            // Если производители совпали смотрим имя
                            if ($data['manufacturer_id'] == $compared_article->manufacturer_id) {

                                // Если имя совпало даем ошибку
                                if ($data['name'] == $compared_article->name) {
                                    $result['msg'] = 'В данной групе существует артикул с таким именем.';
                                    return $result;
                                }
                            }

                            // Убиваем массив, чтоб создать новый
                            unset($compared_article_compositions);
                        }
                    }
                } else {

                    // Состава нет, проверяем производителя
                    foreach ($articles as $compared_article) {

                        // Если производители совпали, проверяем имя
                        if ($data['manufacturer_id'] == $compared_article->manufacturer_id) {

                            // Если имя совпало, даем ошибку
                            if ($data['name'] == $compared_article->name) {
                                $result['msg'] = 'В данной групе существует данный артикул.';
                                return $result;
                            }
                        }
                    }
                }
            }
        }
    }
}