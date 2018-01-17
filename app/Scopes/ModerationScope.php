<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class ModerationScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
      $builder->where('moderated', null);
    }

    public function remove(Builder $builder, Model $model)
    {
        $column = $model->getQualifiedDeletedAtColumn();

        $query = $builder->getQuery();

        foreach ((array) $query->wheres as $key => $where)
        {
            // Если оператор where ограничивает мягкое удаление данных, мы удалим его из
            // запроса и сбросим ключи в операторах where. Это позволит разработчику
            // включить удалённую модель в отношения результирующего набора, который загружается "лениво".
            if ($this->isSoftDeleteConstraint($where, $column))
            {
                unset($query->wheres[$key]);

                $query->wheres = array_values($query->wheres);
            }
        }
    }
    
}