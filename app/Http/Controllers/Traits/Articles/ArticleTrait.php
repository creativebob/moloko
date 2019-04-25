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

        // dd($data);
        // Проверка только если статус черновика не пришел, а сам артикул находится в черновике
        if (!request()->has('draft') && $data['old_draft'] == 1) {

            $articles = Article::with([
                'compositions',
            ])
            ->where([
                'articles_group_id' => $data['articles_group_id'],
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

                // Формируем массив пришедших составов артикула
                if (isset($data['compositions'])) {
                    $article_compositions = [];
                    foreach ($data['compositions'] as $id => $composition) {
                        $article_compositions[$id] = (int) $composition['value'];
                    }
                    // dd($article_compositions);
                }
                

                // Проверяем значения составов
                foreach ($articles as $compared_article) {


                    if ($compared_article->compositions->isNotEmpty()) {
                        // Берем составы для первого найдденного артикула в группе
                        $compared_article_compositions = [];
                        foreach ($compared_article->compositions as $composition) {
                            $compared_article_compositions[$composition->id] = $composition->pivot->value;
                        }
                        // dd($compared_article_compositions);
                    }
                    
                    // Если составы и их значения совпали, то так как один производитель, даем ошибку
                    if (isset($article_compositions) && isset($compared_article_compositions)) {
                        // dd('lol1');
                        if ($article_compositions == $compared_article_compositions) {
                            $result['msg'] = 'В данной групе существует артикул с таким именем и производителем.';
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
    }
}