@if(count($goods_modes) == 1)
<input type="hidden" name="goods_mode_id" value="{{ $goods_modes->first()->id }}">
@else
<label>Тип
	{!! Form::select('goods_mode_id', $goods_modes->pluck('name', 'id')) !!}
</label>
@endif

