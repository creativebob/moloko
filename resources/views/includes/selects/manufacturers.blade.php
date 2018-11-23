 <label>Производитель
 	{{ Form::select('manufacturer_id', $manufacturers->pluck('name', 'id'), $default ?? null, ['placeholder' => 'Выберите производителя', ($cur_goods->goods_article->draft == 1) ? '' : 'disabled']) }}
 </label>
