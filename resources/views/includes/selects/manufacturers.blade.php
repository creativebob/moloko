 <label>Производитель
 	{{ Form::select('manufacturer_id', $manufacturers->pluck('name', 'id'), $manufacturer_id ?? null, ['placeholder' => 'Выберите производителя', (isset($draft)) ? '' : 'disabled']) }}
 </label>
