@if($goods_modes->count() == 1)
{!! Form::hidden('goods_mode_id', $goods_modes->first()->id, []) !!}
@else
<label>Тип
	{!! Form::select('goods_mode_id', $goods_modes->pluck('name', 'id')) !!}
</label>
@endif

