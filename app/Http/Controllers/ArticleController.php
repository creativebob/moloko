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

        $cmvEntities = Entity::with([
            'ancestor:id,model,alias'
        ])
        ->whereHas('type', function ($q) {
            $q->where('alias', 'cmv');
        })
            ->get([
                'name',
                'alias',
                'ancestor_id',
                'model'
            ]);
//        dd($cmvEntities);

        $appointments = [];
        foreach ($cmvEntities as $cmvEntity) {
            if ($cmvEntity->alias === 'goods') {
                $appointments[] = 'cur_goods';
            } else {
                $appointments[] = mb_substr($cmvEntity->alias, 0, -1);
            }
        }
//        dd($appointments);

        $article = Article::with($appointments)
            ->find($request->id);

        $aliases = [];
        foreach ($appointments as $appointment) {
            if (empty($article->$appointment)) {
                if ($appointment === 'cur_goods') {
                    $aliases[] = 'goods';
                } else {
                    $aliases[] = "{$appointment}s";
                }
            }
        }
//        dd($aliases);

        $data = [];
        if (count($aliases) > 0) {
            foreach ($aliases as $alias) {
                $entity = $cmvEntities->firstWhere('alias', $alias);
                if (auth()->user()->can('create', $entity->ancestor->model)) {

                    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
                    $answer = operator_right($entity->ancestor->alias, false, 'index');

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
        $initialItem = $initialModel::find($request->item_id);

        $data['article_id'] = $request->article_id;
        $data['category_id'] = $request->category_id;
        $data['price_unit_category_id'] = $initialItem->price_unit_category_id;
        $data['price_unit_id'] = $initialItem->price_unit_id;

        $model = Entity::where('alias', $request->entity)
            ->value('model');

        $item = $model::firstOrCreate($data);

        $item->load([
            'category',
            'article'
        ]);

        // Пишем к группе связь с категорией
        $item->category->groups()->attach($item->article->articles_group_id);

        return redirect()->back();
    }
}
