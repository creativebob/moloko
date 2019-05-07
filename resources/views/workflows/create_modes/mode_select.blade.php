<div class="small-12 cell up-input-button text-center">
	<a id="mode-default" class="modes">Вернуться</a>
</div>

@if (count($raws_products))
<label>Группа товаров
	<select name="raws_product_id" id="select-raws_products" required>
		@foreach ($raws_products as $raws_product)
		<option value="{{ $raws_product->id }}" data-abbreviation="{{ $raws_product->unit->abbreviation }}">{{ $raws_product->name }}</option>
		@endforeach
	</select>
	{{-- Form::select('raws_product_id', $raws_products_list, null, ['id' => 'raws-products-list']) --}}
</label>
@else
В данной категории нет групп, выберите другую категорию или <a id="mode-default" class="modes">вернитесь назад</a>
@endif

{{ Form::hidden('mode', 'mode-select', ['id' => 'mode']) }}