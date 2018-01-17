<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class ModerationScope implements Scope
{


  /**
 * Применение заготовки к указанному конструктору запросов Eloquent.
 *
 * @param  \Illuminate\Database\Eloquent\Builder  $builder
 * @param  \Illuminate\Database\Eloquent\Model  $model
 * @return void
 */
public function apply(Builder $builder, Model $model)
{
    $builder->whereNull($model->getQualifiedModeratedAtColumn());

    $this->extend($builder);
}

/**
 * Удаление заготовки из указанного конструктора запросов Eloquent.
 *
 * @param  \Illuminate\Database\Eloquent\Builder  $builder
 * @param  \Illuminate\Database\Eloquent\Model  $model
 * @return void
 */
public function remove(Builder $builder, Model $model)
{
    $column = $model->getQualifiedModeratedAtColumn();

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
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    // public function apply(Builder $builder, Model $model)
    // {
    //   $builder->where('moderated_at', '!=', null);
    // }
}