<div class="reveal" id="first-add" data-reveal data-close-on-click="false">
	<div class="grid-x">
		<div class="small-12 cell modal-title">
			<h5>ДОБАВЛЕНИЕ товара</h5>
		</div>
	</div>
	{{ Form::open(['url' => '/admin/goods','id'=>'form-cur-goods-add', 'data-abide', 'novalidate']) }}
	<div class="grid-x grid-padding-x align-center modal-content inputs">
		<div class="small-10 cell">

			<div class="grid-x grid-margin-x">

				<div class="small-12 cell">

					<label>Категория товаров
						<select name="goods_category_id" id="goods-categories-list" required>
							<!-- <option value="0">Выберите категорию</option> -->
							@php
							echo $goods_categories_list;
							@endphp
						</select>
					</label>
				</div>

				<div class="small-9 cell">
					<label>Название товара
						@include('includes.inputs.string', ['value' => null, 'name' => 'name', 'required' => 'required'])
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
							<label>Категория единиц измерения
								{{ Form::select('units_category_id', $units_categories_list, 6, ['id' => 'units-categories-list', 'required']) }}
							</label>
						</div>

						<div class="small-12 medium-6 cell">
							<label>Единица измерения
								<select name="unit_id" id="units-list" required>
									@include('goods.units_list', $units)
								</select>
								{{-- Form::select('unit_id', $units_list, 26, ['id' => 'units-list', 'required']) --}}
							</label>
						</div>
					</div>
				</div>

				<div class="small-10 medium-4 cell">
					<label>Цена за (<span id="unit-change" class="unit-change">{{ $unit_abbreviation }}</span>)
						{{ Form::number('price') }}
					</label>
				</div>

			</div>


			<div class="small-12 cell checkbox">
				{{ Form::checkbox('quickly', 1, null, ['id' => 'quickly-goods', 'checked']) }}
				<label for="quickly-goods"><span>Быстрое добавление</span></label>
			</div>

			@can('god', App\Goods::class)
			<div class="checkbox">
				{{ Form::checkbox('system_item', 1, null, ['id' => 'system-item-position']) }}
				<label for="system-item-position"><span>Системная запись.</span></label>
			</div>
			@endcan

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




