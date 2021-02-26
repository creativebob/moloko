<?php

namespace App\Http\Controllers;

use App\Article;
use App\Entity;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    /**
     * Получаем доступные для назанчения сущности ТМЦ с категориями
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAppointments(Request $request)
    {
        $appointments = [
            'raw',
            'container',
            'cur_goods',
            'attachment',
            'tool',
            'impact'
        ];

        $article = Article::with($appointments)
            ->find($request->id);

        $relations = [];
        foreach ($appointments as $appointment) {
            if (empty($article->$appointment)) {
                $relations[] = $appointment;
            }
        }

        $data = [];

        if (count($relations) > 0) {
            $aliases = [];
            foreach ($relations as $key => $alias) {
                switch ($alias) {
                    case('raw'):
                        $aliases[] = 'raws';
                        break;

                    case('container'):
                        $aliases[] = 'containers';
                        break;

                    case('cur_goods'):
                        $aliases[] = 'goods';
                        break;

                    case('attachment'):
                        $aliases[] = 'attachments';
                        break;

                    case('tool'):
                        $aliases[] = 'tools';
                        break;

                    case('impact'):
                        $aliases[] = 'impacts';
                        break;
                }
            }

            $entities = Entity::with([
                'ancestor:id,model'
            ])
                ->whereIn('alias', $aliases)
                ->get([
                    'name',
                    'alias',
                    'ancestor_id'
                ]);

            foreach ($entities as $entity) {
                // Получаем из сессии необходимые данные (Функция находиться в Helpers)
                $answer = operator_right($entity->alias, false, 'index');

                $categories = $entity->ancestor->model::moderatorLimit($answer)
                    ->companiesLimit($answer)
                    ->get([
                        'id',
                        'name'
                    ]);

//                $categoriesTree = buildTree($categories);
                $data['entities'][] = [
                    'name' => $entity->name,
                    'alias' => $entity->alias,
                    'categories' => $categories,
                ];
            }
        }
        return response()->json($data);
    }

    /**
     * Назначаем ТМЦ
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function appointment(Request $request)
    {
        $initialModel = Entity::where('alias', $request->initial_entity)
            ->value('model');
        $item = $initialModel::select([
            'id',
            'price_unit_category_id',
            'price_unit_id'
        ])
        ->find($request->item_id);

        $data['article_id'] = $request->article_id;
        $data['category_id'] = $request->category_id;
        $data['price_unit_category_id'] = $item->price_unit_category_id;
        $data['price_unit_id'] = $item->price_unit_id;

        $model = Entity::where('alias', $request->entity)
            ->value('model');

        $item = $model::firstOrCreate($data);

        return redirect()->back();
    }
}
