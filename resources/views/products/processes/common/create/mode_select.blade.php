@if ($articles_groups->isNotEmpty())
<div class="small-12 cell up-input-button text-center">
	<a id="mode-default" class="modes">Вернуться</a>
</div>

<label>Группа товаров
	<select name="articles_group_id" id="select-articles_groups" required>

		@foreach ($articles_groups as $articles_group)
		<option value="{{ $articles_group->id }}" data-abbreviation="{{ $articles_group->unit->abbreviation }}">{{ $articles_group->name }}</option>
		@endforeach

	</select>
	{{-- Form::select('goods_product_id', $goods_products_list, null, ['id' => 'goods-products-list']) --}}
</label>

@else

В данной категории нет групп, выберите другую категорию или <a id="mode-default" class="modes">вернитесь назад</a>

@endif

{{ Form::hidden('mode', 'mode-select', ['id' => 'mode']) }}