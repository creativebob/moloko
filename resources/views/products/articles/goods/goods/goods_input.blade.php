<tr class="item" id="table-goods-{{ $cur_goods->id }}" data-name="{{ $cur_goods->article->name }}">
	<td>

		{{ $cur_goods->category->name }}

	</td>
	<td>{{ $cur_goods->article->name }}</td>
	<td>
		<div class="wrap-input-table">
			{{-- Количество чего-либо --}}
			{{ Form::text('goods['.$cur_goods->id.'][value]', $cur_goods->pivot ? $cur_goods->pivot->value : null, ['class'=>'digit-field name-field compact w12 padding-to-placeholder goods-value', 'id'=>'1', 'maxlength'=>'7', 'autocomplete'=>'off', 'pattern'=>'[0-9\W\s]{0,10}', 'placeholder'=>'0', !empty($disabled) ? 'disabled' : '', 'required']) }}
			<label for="1" class="text-to-placeholder">

				{{ $cur_goods->article->group->unit->abbreviation }}

			</label>
			<div class="sprite-input-right find-status" id="name-check"></div>
			<span class="form-error">Введите количество</span>
		</div>
	</td>

	<td class="td-delete">
		@empty($disabled)
			<a class="icon-delete sprite" data-open="delete-item"></a>
		@endempty
	</td>
</tr>
