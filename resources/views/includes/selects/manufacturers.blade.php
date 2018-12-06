 <label>Производитель
 	{{ Form::select('manufacturer_id', $manufacturers->pluck('name', 'id'), $manufacturer_id ?? null, [(isset($draft)) ? '' : 'disabled']) }}
 </label>
