<div class="reveal" id="first-add" data-reveal data-close-on-click="false">
	<div class="grid-x">
		<div class="small-12 cell modal-title">
			<h5>ДОБАВЛЕНИЕ услуги</h5>
		</div>
	</div>
	{{ Form::open(['url' => '/admin/services','id'=>'form-service-add', 'data-abide', 'novalidate']) }}
	<div class="grid-x grid-padding-x align-center modal-content inputs">
		<div class="small-12 cell">

			<div class="grid-x cell">
				<div class="small-12 cell">

					<label>Выберите категорию
						<select name="services_category_id" id="services-categories-list" class="mode-default" required>
							<!-- <option value="0">Выберите категорию</option> -->
							@php
							echo $services_categories_list;
							@endphp
						</select>
					</label>

				</div>

				<div id="mode" class="small-12 cell relative">
					@include('services.mode-default')
				</div>

				<div class="small-10 medium-4 cell">
					<label>Цена
						{{ Form::number('price') }}
					</label>
				</div>

			</div>



			{{-- Чекбокс отображения на сайте
				@can ('publisher', App\Service::class)
				<div class="small-12 cell checkbox">
					{{ Form::checkbox('display', 1, null, ['id' => 'display-service']) }}
					<label for="display-service"><span>Отображать на сайте</span></label>
				</div>
				@endcan  --}}

				<div class="small-12 cell checkbox">
					{{ Form::checkbox('quickly', 1, null, ['id' => 'quickly-service', 'checked']) }}
					<label for="quickly-service"><span>Быстрое добавление</span></label>
				</div>

				@can('god', App\Service::class)
				<div class="checkbox">
					{{ Form::checkbox('system_item', 1, null, ['id' => 'system-item-service']) }}
					<label for="system-item-service"><span>Системная запись.</span></label>
				</div>
				@endcan

			</div>
		</div>
		<div class="grid-x align-center">
			<div class="small-6 medium-4 cell">
				{{ Form::submit('Добавить услугу', ['class'=>'button modal-button']) }}
			</div>
		</div>
		{{ Form::close() }}
		<div data-close class="icon-close-modal sprite close-modal add-item"></div> 
	</div>






