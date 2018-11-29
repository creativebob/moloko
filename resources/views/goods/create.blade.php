<div class="reveal" id="modal-create" data-reveal data-close-on-click="false">
	<div class="grid-x">
		<div class="small-12 cell modal-title">
			<h5>ДОБАВЛЕНИЕ товара</h5>
		</div>
	</div>
	{{ Form::open(['url' => '/admin/goods','id'=>'form-cur_goods-create', 'data-abide', 'novalidate']) }}
	<div class="grid-x grid-padding-x align-center modal-content inputs">
		<div class="small-10 cell">

			<div class="grid-x grid-margin-x">

				<div class="small-12 cell">

					<label>Категория товаров
						@include('includes.selects.goods_categories', ['entity' => 'goods_categories'])

					</label>
				</div>

				<div class="small-9 cell">
					<label>Название товара
						@include('includes.inputs.string', ['value' => null, 'name' => 'name', 'required' => true])
						<div class="item-error">Названия товара и группы товаров не должны совпадать!</div>
					</label>
				</div>

				<div class="small-3 cell checkbox set-status">
					{{ Form::checkbox('set_status', 'set', null, ['id' => 'set-status']) }}
					<label for="set-status"><span>Набор</span></label>
				</div>

				<div id="mode" class="small-12 cell relative">
					@include('goods.create_modes.mode_default')
				</div>

				<div class="small-12 cell">
					<div class="grid-x grid-margin-x" id="units-block">
						<div class="small-12 medium-6 cell">
							@include('includes.selects.units_categories', ['default' => 6])
						</div>

						<div class="small-12 medium-6 cell">
							@include('includes.selects.units', ['default' => 26, 'units_category_id' => 6])
						</div>
					</div>
				</div>

				<div class="small-10 medium-4 cell">
					<label>Цена за (<span id="unit-change" class="unit-change"></span>)
						{{ Form::number('price') }}
					</label>
				</div>
			</div>

			<div class="small-12 cell checkbox">
				{{ Form::checkbox('quickly', 1, null, ['id' => 'quickly', 'checked']) }}
				<label for="quickly"><span>Быстрое добавление</span></label>
			</div>

			@include('includes.control.checkboxes', ['item' => $cur_goods])

		</div>
	</div>
	<div class="grid-x align-center">
		<div class="small-6 medium-4 cell">
			{{ Form::submit('Добавить товар', ['data-close', 'class'=>'button modal-button']) }}
		</div>
	</div>
	{{ Form::close() }}
	<div data-close class="icon-close-modal sprite close-modal add-item"></div>
</div>

<script>
	$('#unit-change').text($('#select-units :selected').data('abbreviation'));
</script>




