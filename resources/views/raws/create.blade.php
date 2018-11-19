<div class="reveal" id="first-add" data-reveal data-close-on-click="false">
	<div class="grid-x">
		<div class="small-12 cell modal-title">
			<h5>ДОБАВЛЕНИЕ сырья</h5>
		</div>
	</div>
	{{ Form::open(['url' => '/admin/raws','id'=>'form-raw-add', 'data-abide', 'novalidate']) }}
	<div class="grid-x grid-padding-x align-center modal-content inputs">
		<div class="small-10 cell">

			<div class="grid-x cell">
				<div class="small-12 cell">

					<label>Выберите категорию
						<select name="raws_category_id" id="raws-categories-list" class="mode-default"  required>
							<!-- <option value="0">Выберите категорию</option> -->
							@php
							echo $raws_categories_list;
							@endphp
						</select>
					</label>

				</div>

				<div id="mode" class="small-12 cell relative">
					@include('raws.mode-default')
				</div>

				<div class="grid-x grid-margin-x">
					<div class="small-12 medium-6 cell">
						<label>Категория единиц измерения
							{{ Form::select('units_category_id', $units_categories_list, null, ['placeholder' => 'Выберите категорию', 'id' => 'units-categories-list', 'required']) }}
						</label>
					</div>
					<div class="small-12 medium-6 cell">
						<label>Единица измерения
							<select name="unit_id" id="units-list" required disabled></select>
						</label>
					</div>
				</div>


				<div class="small-10 medium-4 cell">
					<label>Себестоимость
						{{ Form::number('cost') }}
					</label>
				</div>

			</div>

			<div class="small-12 cell checkbox">
				{{ Form::checkbox('quickly', 1, null, ['id' => 'quickly-raws', 'checked']) }}
				<label for="quickly-raws"><span>Быстрое добавление</span></label>
			</div>

			@can('god', App\Raw::class)
			<div class="checkbox">
				{{ Form::checkbox('system_item', 1, null, ['id' => 'system-item-position']) }}
				<label for="system-item-position"><span>Системная запись.</span></label>
			</div>
			@endcan

		</div>
	</div>
	<div class="grid-x align-center">
		<div class="small-6 medium-4 cell">
			{{ Form::submit('Добавить сырье', ['data-close', 'class'=>'button modal-button']) }}
		</div>
	</div>
	{{ Form::close() }}
	<div data-close class="icon-close-modal sprite close-modal add-item"></div>
</div>




