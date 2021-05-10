<?php

namespace App\Http\Controllers\System\Traits;


use App\Seo;
use App\SeosParam;

trait Seoable
{

    public function updateSeo($item)
    {
//        dd(request()->additional_seos);
        $request = request();
//        dd($request->seo);
        $requestSeo = $request->seo;

        $columns = [
            'title',
            'h1',
            'description',
            'keywords',
            'content',
            'is_canonical'
        ];

        $data = [];
        foreach($columns as $column) {
            $data[$column] = isset($requestSeo[$column]) ? $requestSeo[$column] : null;
        }

        $itemSeo = $item->seo;
        // СЕО
        if (isset($itemSeo)) {
            $itemSeo->update($requestSeo);
            $seo = $itemSeo;
        } else {
            $seo = Seo::create($requestSeo);
            $item->update([
                'seo_id' => $seo->id
            ]);
        }

        $seo->load('childs');

        $oldAdditionalSeosIds = $seo->childs->pluck('id')->toArray();
        $additionalSeosIds = [];

        $hasAdditionals = $request->has('additional_seos');

        // Дополнительные СЕО
        if ($hasAdditionals) {

            foreach($request->additional_seos as $requestAdditionalSeo) {

                $additionalData = [];
                foreach($columns as $column) {
                    $additionalData[$column] = isset($requestAdditionalSeo[$column]) ? $requestAdditionalSeo[$column] : null;
                }
                $additionalData['parent_id'] = $seo->id;

                if (isset($requestAdditionalSeo['id'])) {
                    $additionalSeo = $seo->childs->firstWhere('id', $requestAdditionalSeo['id']);
                    $additionalSeo->update($additionalData);
                    $additionalSeosIds[] = $additionalSeo->id;

                    $oldAdditionalSeosParamsIds = $additionalSeo->params->pluck('id')->toArray();
                    $additionalSeosParamsIds = [];

                    foreach ($requestAdditionalSeo['params'] as $param) {
                        if (isset($param['id'])) {
//                            $additionalSeoParam = $additionalSeo->params->firstWhere('id', $param['id']);
//                            $additionalSeoParam->update($param);
                            $additionalSeosParamsIds[] = $param['id'];
                        } else {
                            $paramData = $param;
                            $paramData['seo_id'] = $additionalSeo->id;
                            $param = SeosParam::create($paramData);
                            $additionalSeosParamsIds[] = $param->id;
                        }
                    }
                    $deleteParamsIds = array_diff($oldAdditionalSeosParamsIds, $additionalSeosParamsIds);
                    $res = SeosParam::destroy($deleteParamsIds);
                } else {
                    $additionalSeo = Seo::create($additionalData);
                    $additionalSeosIds[] = $additionalSeo->id;

                    $params = [];
                    foreach ($requestAdditionalSeo['params'] as $param) {
                        $params[] = SeosParam::make($param);
                    }
                    $additionalSeo->params()->saveMany($params);
                }
            }
        }
        $deleteIds = array_diff($oldAdditionalSeosIds, $additionalSeosIds);
        $res = Seo::destroy($deleteIds);

        // Если нет доп сео и пустые поля - удаляем
        if (!$hasAdditionals) {
            $values = array_filter($data);

            if (count($values) == 0) {
                $item->update([
                    'seo_id' => null
                ]);
                $seo->delete();
            }
        }
    }
}
