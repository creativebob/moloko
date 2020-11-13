<?php

namespace App\Models\System;

use App\Company;
use App\User;
use Illuminate\Database\Eloquent\Model;
use App\Scopes\Traits\CompaniesLimitTraitScopes;
use App\Scopes\Traits\AuthorsTraitScopes;
use App\Scopes\Traits\SystemItemTraitScopes;
use App\Scopes\Traits\FilialsTraitScopes;
use App\Scopes\Traits\TemplateTraitScopes;
use App\Scopes\Traits\ModeratorLimitTraitScopes;
use App\Scopes\Traits\SuppliersTraitScopes;
use App\Scopes\Filters\Filter;
use App\Scopes\Filters\BooklistFilter;

class BaseModel extends Model
{
    use CompaniesLimitTraitScopes;
    use AuthorsTraitScopes;
    use SystemItemTraitScopes;
    use FilialsTraitScopes;
    use TemplateTraitScopes;
    use ModeratorLimitTraitScopes;
    use SuppliersTraitScopes;
//    use Filter;
    use BooklistFilter;

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Получение фильтров для сущности
     *
     * @param $alias
     * @return array
     */
    public function getFilters($alias)
    {
        $request = request();
        $requestInput = $request->input();

        $user = auth()->user()->load('filters');
        $filter = $user->filters->firstWhere('alias', $alias);

        $filters = [];
        // TODO - 10.11.20 - Костыль с пагинацией
        if (count($requestInput)) {
            $filters = $requestInput;

            $data = [
                'alias' => $alias,
                'filters' => json_encode($filters)
            ];

            if ($filter) {
                $filter->update($data);
            } else {
                $user->filters()->create($data);
            }
        } else {
            if ($filter) {
                $filters = $filter->filtersArray;
                $request->request->add($filter->filtersArray);
            }
        }

        return $filters;
    }
}
