

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

				{{-- <div id="mode" class="small-12 cell relative">
					@include('tmc.create.mode_default')
				</div> --}}

				<div class="small-12 cell">
					<label>Название
						@include('includes.inputs.string', ['value' => null, 'name' => 'name', 'required' => true])
						<div class="item-error">Названия артикула и группы артикулов не должны совпадать!</div>
					</label>
				</div>

                {!! Form::hidden('unit_id', 32, []) !!}
                {!! Form::hidden('mode', 'mode-default', []) !!}

            </div>


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




