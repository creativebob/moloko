<div class="reveal" id="medium-add" data-reveal data-close-on-click="false">
	<div class="grid-x">
		<div class="small-12 cell modal-title">
			<h5>ДОБАВЛЕНИЕ категории услуг</h5>
		</div>
	</div>
	{{-- <div class="grid-x tabs-wrap align-center tabs-margin-top">
		<div class="small-8 cell">
			<ul class="tabs-list" data-tabs id="tabs">
				<li class="tabs-title is-active"><a href="#add-category" aria-selected="true">Подкатегория</a></li>
				<li class="tabs-title"><a data-tabs-target="add-service" href="#add-service">Группа</a></li>
			</ul>
		</div>
	</div> --}}

	<div class="tabs-wrap inputs">
		<div class="tabs-content" data-tabs-content="tabs">
			<!-- Добавляем категорию -->
			<div class="tabs-panel is-active" id="add-category">
				{{ Form::open(['id'=>'form-medium-add', 'data-abide', 'novalidate']) }}

				<!-- Основные -->
				<div class="grid-x grid-padding-x align-center modal-content inputs">
					<div class="small-10 cell">
						<label>Категория
							<select name="parent_id">
								@php
								echo $goods_categories_list;
								@endphp
							</select>
						</label>
						<label>Название подкатегории
							@include('includes.inputs.name', ['value'=>null, 'name'=>'name', 'required'=>'required'])
							<div class="sprite-input-right find-status"></div>
							<div class="item-error">Такой уже существует!</div>
						</label>
					</div>
				</div>

				{{ Form::hidden('goods_category_id', $goods_category->id, ['id' => 'goods-category-id']) }}
				{{ Form::hidden('medium_item', 1, ['class' => 'medium-item', 'pattern' => '[0-9]{1}']) }}
				{{ Form::hidden('category_id', 0, ['class' => 'category-id']) }}

				<div class="grid-x align-center">

					{{-- Чекбокс отображения на сайте --}}
					@can ('publisher', $goods_category)
					<div class="small-8 cell checkbox">
						{{ Form::checkbox('display', 1, $goods_category->display, ['id' => 'display']) }}
						<label for="display"><span>Отображать на сайте</span></label>
					</div>
					@endcan

					@if ($goods_category->moderation == 1)
					<div class="small-8 cell checkbox">
						{{ Form::checkbox('moderation', 1, $goods_category->moderation, ['id' => 'moderation']) }}
						<label for="moderation"><span>Временная запись.</span></label>
					</div>
					@endif

					@can('god', App\GoodsCategory::class)
					<div class="small-8 cell checkbox">
						{{ Form::checkbox('system_item', 1, $goods_category->system_item, ['id' => 'system-item']) }}
						<label for="system-item"><span>Системная запись.</span></label>
					</div>
					@endcan
				</div>

				<div class="grid-x align-center">
					<div class="small-6 medium-6 cell">
						{{ Form::submit('Добавить подкатегорию услуг', ['data-close', 'class'=>'button modal-button submit-add']) }}
					</div>
				</div>

				{{ Form::close() }}

			</div>
			
		</div>
		<div data-close class="icon-close-modal sprite close-modal add-item"></div> 
	</div>

	@include('includes.scripts.inputs-mask')
	@include('includes.scripts.upload-file')
	@include('goods_categories.scripts')
</div>



