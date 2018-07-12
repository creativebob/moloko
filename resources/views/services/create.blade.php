<div class="reveal" id="first-add" data-reveal data-close-on-click="false">
	<div class="grid-x">
		<div class="small-12 cell modal-title">
			<h5>ДОБАВЛЕНИЕ услуги</h5>
		</div>
	</div>
	{{ Form::open(['url' => '/services','id'=>'form-service-add', 'data-abide', 'novalidate']) }}
	<div class="grid-x grid-padding-x align-center modal-content inputs">
		<div class="small-12 cell">

			<div class="grid-x cell">
				<div class="small-12 cell">

					<label>Категория
						<select name="services_category_id" id="services-categories-list" required>
							<option value="0">Выберите категорию</option>
							@php
							echo $services_categories_list;
							@endphp
						</select>
					</label>

				</div>

				<div id="mode" class="small-12 cell">
					
				</div>

			</div>



			{{-- Чекбокс отображения на сайте --}}
			@can ('publisher', App\Service::class)
			<div class="small-12 cell checkbox">
				{{ Form::checkbox('display', 1, null, ['id' => 'display-position']) }}
				<label for="display-position"><span>Отображать на сайте</span></label>
			</div>
			@endcan

			@can('god', App\Service::class)
			<div class="checkbox">
				{{ Form::checkbox('system_item', 1, null, ['id' => 'system-item-position']) }}
				<label for="system-item-position"><span>Системная запись.</span></label>
			</div>
			@endcan

		</div>
	</div>
	<div class="grid-x align-center">
		<div class="small-6 medium-4 cell">
			{{ Form::submit('Добавить услугу', ['data-close', 'class'=>'button modal-button submit-service-product-add']) }}
		</div>
	</div>
	{{ Form::close() }}
	<div data-close class="icon-close-modal sprite close-modal add-item"></div> 
</div>

@include('includes.scripts.inputs-mask')
@include('includes.scripts.upload-file')
@include('services.scripts')




