<?php
	
namespace App\Scopes

use Illuminate\Datebase\Eloquent\Scope
use Illuminate\Database\Eloquent\Model
use Illiminate\Database\Eloquent\Builder


class AgeScope implements Scope
{
  /**
   * Применение заготовки к данному построителю запросов Eloquent.
   *
   * @param  \Illuminate\Database\Eloquent\Builder  $builder
   * @param  \Illuminate\Database\Eloquent\Model  $model
   * @return void
   */
  public function apply(Builder $builder, Model $model)
  {
    $builder->where('age', '>', 200);
  }
}


?>