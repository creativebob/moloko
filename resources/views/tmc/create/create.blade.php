

<div class="reveal" id="modal-create" data-reveal data-close-on-click="false">
	<div class="grid-x">
		<div class="small-12 cell modal-title">
			<h5>{{ $title }}</h5>
		</div>
	</div>
	{{ Form::open(['route' => $entity.'.store', 'id' => 'form-create', 'data-abide', 'novalidate']) }}
	<div class="grid-x grid-padding-x align-center modal-content inputs">
		<div class="small-10 cell">

			<div class="grid-x grid-margin-x">

				<div class="small-12 cell">

					<label>Категория
						@include('includes.selects.categories', [
							'category_entity' => $category_entity
						]
						)
					</label>
				</div>

				<div id="mode" class="small-12 cell relative">
					@include('tmc.create.mode_default')
				</div>

				<div class="small-12 cell">
					<label>Название
						@include('includes.inputs.string', ['value' => null, 'name' => 'name', 'required' => true])
						<div class="item-error">Названия артикула и группы артикулов не должны совпадать!</div>
					</label>
				</div>

				<div class="small-12 cell">
					<div class="grid-x grid-margin-x" id="units-block">
						<div class="small-12 medium-6 cell">
							@include('includes.selects.units_categories', ['default' => 6, 'type' => 'tmc'])
						</div>

						<div class="small-12 medium-6 cell">
							@include('includes.selects.units', ['default' => 26, 'units_category_id' => 6])
						</div>
					</div>

                    <div class="grid-x grid-margin-x" id="extra-units-block">
                        <div class="small-12 medium-6 cell">
                            <label>Введите вес
                            {!! Form::number('weight', null, []) !!}
                            </label>
                        </div>

                        <div class="small-12 medium-6 cell">
                            @include('includes.selects.units_extra', ['default' => 8, 'units_category_id' => 2])
                        </div>
                    </div>
				</div>


				<div class="small-10 medium-4 cell">

					<label>Себестоимость за (<span id="unit-change" class="unit-change"></span>)
						{{-- @include('includes.scripts.class.digitfield')
						@include('includes.inputs.digit', ['name' => 'price_default', 'value' => null, 'decimal_place' => 2]) --}}
						{{ Form::number('price_default') }}
					</label>
				</div>
			</div>

			@includeIf($entity.'.create')

			<div class="small-12 cell checkbox set-status">
				{{ Form::checkbox('set_status', 1, null, ['id' => 'set-status']) }}
				<label for="set-status"><span>Набор</span></label>
			</div>

			<div class="small-12 cell checkbox">
				{{ Form::checkbox('quickly', 1, null, ['id' => 'quickly', 'checked']) }}
				<label for="quickly"><span>Быстрое добавление</span></label>
			</div>

			@include('includes.control.checkboxes', ['item' => $item])

		</div>
	</div>
	<div class="grid-x align-center">
		<div class="small-6 medium-4 cell">
			{{ Form::submit('Добавить', ['class' => 'button modal-button']) }}
		</div>
	</div>
	{{ Form::close() }}
	<div data-close class="icon-close-modal sprite close-modal add-item"></div>
</div>

<script>
	$('#unit-change').text($('#select-units :selected').data('abbreviation'));
</script>



