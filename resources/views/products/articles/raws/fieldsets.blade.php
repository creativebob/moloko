<fieldset class="fieldset portion-goods-fieldset" id="portion-goods-fieldset">
	<legend class="checkbox">
		{{ Form::checkbox('portion_goods_status', 1, $raw->portion_goods_status, ['id' => 'portion_goods_status', $disabled ? 'disabled' : '']) }}
		<label for="portion_goods_status">
			<span id="portion-goods-change">Добавлять в состав товара порциями</span>
		</label>
	</legend>

	<div class="grid-x grid-margin-x" id="portion-goods-block">
		{{-- <div class="small-12 cell @if ($raw->portion_goods_status == null) portion-goods-hide @endif">
            <label>Имя&nbsp;порции
                {{ Form::text('portion_goods_name', $raw>portion_name, ['id'=>'portion_goods_name', class'=>'text-field name-field compact', 'maxlength'=>'40', 'autocomplete'=>'off', 'pattern'=>'[0-9\W\s]{0,10}', $disabled ? 'disabled' : ''], ['required']) }}
            </label>
        </div> --}}
		<div class="small-6 cell @if ($raw->portion_goods_status == null) portion-goods-hide @endif">
			<label>Сокр.&nbsp;имя
				{{ Form::text('portion_goods_abbreviation',  $raw->portion_goods_abbreviation, ['id'=>'portion_goods_abbreviation', 'class'=>'text-field name-field compact', 'maxlength'=>'40', 'autocomplete'=>'off', 'pattern'=>'[0-9\W\s]{0,10}', $disabled ? 'disabled' : ''], ['required']) }}
			</label>
		</div>

		<div class="small-4 cell @if ($raw->portion_goods_status == null) portion-goods-hide @endif">
			<label>Единица измерения
				@include('products.articles.common.edit.select_units', [
					'id' => 'select_units_portion_goods',
                    'field_name' => 'unit_portion_goods_id',
                    'units_category_id' => $article->unit->category_id,
                    'disabled' => $disabled,
                    'data' => $raw->unit_portion_goods_id ?? $raw->article->unit_id,
                ])
			</label>
		</div>

		<div class="small-2 cell @if ($raw->portion_goods_status == null) portion-goods-hide @endif">
			<label>Кол-во
				{{ Form::text('portion_goods_count', $raw->portion_goods_count, ['id'=>'portion_goods_count', 'class'=>'digit-field name-field compact', 'maxlength'=>'40', 'autocomplete'=>'off', 'pattern'=>'[0-9\W\s]{0,10}', $disabled ? 'disabled' : ''], ['required']) }}
				<div class="sprite-input-right find-status" id="name-check"></div>
				<span class="form-error">Введите количество</span>
			</label>
		</div>
	</div>
</fieldset>

