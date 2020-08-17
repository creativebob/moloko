@extends('layouts.app')

@section('inhead')
    @include('includes.scripts.dropzone-inhead')
    @include('includes.scripts.fancybox-inhead')
    @include('includes.scripts.sortable-inhead')

    @if ($entity == 'goods')
        @include('includes.scripts.chosen-inhead')
        @include('products.articles.goods.attachments.class')
        @include('products.articles.goods.raws.class')
        @include('products.articles.goods.containers.class')
    @endif

@endsection

@section('title', $title)

@section('breadcrumbs', Breadcrumbs::render('alias-edit', $pageInfo, $article))

@section('title-content')
	<div class="top-bar head-content">
		<div class="top-bar-left">
			<h2 class="header-content">{{ $title }} &laquo{{ $article->name }}&raquo</h2>
		</div>
		<div class="top-bar-right">
		</div>
	</div>
@endsection

@php
	$disabled = $article->draft == 0 ? true : null;
@endphp

@section('content')
<div class="grid-x tabs-wrap">
	<div class="small-12 cell">
		<ul class="tabs-list" data-tabs id="tabs">

			<li class="tabs-title is-active">
				<a href="#tab-options" aria-selected="true">Общая информация</a>
			</li>

			{{-- Табы для сущности --}}
			@includeIf($pageInfo->entity->view_path . '.tabs')

			<li class="tabs-title">
				<a data-tabs-target="tab-photos" href="#tab-photos">Фотографии</a>
			</li>

			<li class="tabs-title">
				<a data-tabs-target="tab-extra_options" href="#tab-extra_options">Опции</a>
			</li>

            @can('index', App\Site::class)
                <li class="tabs-title">
                    <a data-tabs-target="tab-site" href="#tab-site">Настройка для сайта</a>
                </li>
            @endcan

		</ul>
	</div>
</div>

<div class="grid-x tabs-wrap inputs">
	<div class="small-12 cell tabs-margin-top">
		<div class="tabs-content" data-tabs-content="tabs">

			{{ Form::model($article, [
				'route' => [$entity.'.update', $item->id],
				'data-abide',
				'novalidate',
				'files' => 'true',
				'id' => 'form-edit'
			]
			) }}
            @method('PATCH')

            {!! Form::hidden('paginator_url', $paginator_url ?? null) !!}

			{{-- Общая информация --}}
			<div class="tabs-panel is-active" id="tab-options">

				{{-- Разделитель на первой вкладке --}}
				<div class="grid-x grid-padding-x">

					{{-- Левый блок на первой вкладке --}}
					<div class="small-12 large-6 cell">

						{{-- Основная инфа --}}
						<div class="grid-x grid-margin-x">
							<div class="small-12 medium-6 cell">

								<label>Название
									{{ Form::text('name', $article->name, ['required']) }}
								</label>

								<articles-categories-with-groups-component :item="{{ $item }}" :article="{{ $article }}" :categories='@json($categories_tree)' :groups='@json($groups)'></articles-categories-with-groups-component>

								<label>Производитель
                                    @if ($item->category->manufacturers->isNotEmpty())
                                        @if($item->getTable() == 'goods')
                                            <manufacturers-component
                                                :item='@json($item)'
                                                :manufacturers='@json($item->category->manufacturers)'
                                                disabled="{{ $disabled }}"
                                            ></manufacturers-component>
                                        @else
                                            {!! Form::select('manufacturer_id', $item->category->manufacturers->pluck('company.name', 'id'), $article->manufacturer_id, [$disabled ? 'disabled' : '']) !!}
                                        @endif
                                    @else
                                        @if($item->getTable() == 'goods')
                                            @include('products.articles.common.edit.manufacturers')
                                        @else
                                            @include('includes.selects.manufacturers')
                                        @endif
                                    @endif
								</label>

								<div class="grid-x grid-margin-x">
									<div class="small-12 medium-6 cell">
										<label>Единица измерения
											@include('products.articles.common.edit.select_units', [
												'units_category_id' => $article->unit->category_id,
												'disabled' => null,
												'data' => $article->unit_id,
											])
										</label>
									</div>
									{{-- <div class="small-12 medium-6 cell">
										@isset ($article->unit_id)
											@if($article->group->units_category_id != 2)
												<label>Вес единицы, {{ $article->weight_unit->abbreviation }}
													{!! Form::number('weight', null, ['disabled' => ($article->draft == 1) ? null : true]) !!}
												</label>
											@else
												{{ Form::hidden('weight', $article->weight) }}
											@endif
										@endisset
									</div> --}}
								</div>


								{{-- Если указана ед. измерения - ШТ. --}}
								 @if($item->getTable() == 'goods')
									@if($article->group->units_category_id == 6)
										<div class="cell small-12 block-price-unit">
											<fieldset class="minimal-fieldset">
												<legend>Единица для определения цены</legend>
												<div class="grid-x grid-margin-x">
													<div class="small-12 medium-6 cell">
														@include('includes.selects.units_categories', [
															'default' => 6,
															'data' => $item->price_unit_category_id,
															'type' => 'article',
															'name' => 'price_unit_category_id',
															'id' => 'select-price-units_categories',
														])
													</div>
													<div class="small-12 medium-6 cell">
														@include('includes.selects.units', [
															'default' => 32,
															'data' => $item->price_unit_id,
															'units_category_id' => $item->price_unit_category_id,
															'name' => 'price_unit_id',
															'id' => 'select-price-units',
														])
													</div>
												</div>
											</fieldset>
										</div>
									@endif
								 @endif
								{!! Form::hidden('id', null, ['id' => 'item-id']) !!}




							</div>

							<div class="small-12 medium-6 cell">
								<div class="small-12 cell">
									<label>Фотография
										{{ Form::file('photo') }}
									</label>
									<div class="text-center wrap-article-photo">
										<img id="photo" src="{{ getPhotoPathPlugEntity($item) }}">
									</div>
								</div>
							</div>
						</div>

					</div>
					{{-- Конец левого блока на первой вкладке --}}

					{{-- Правый блок на первой вкладке --}}
					<div class="small-12 large-6 cell">

						<div class="grid-x">
							<div class="small-12 cell">
								<label>Описание
									@include('includes.inputs.textarea', ['name' => 'description', 'value' => $article->description])
								</label>
							</div>
							@if($article->group->units_category_id != 2)
								<div class="cell small-12">
									<div class="grid-x grid-margin-x">
										<div class="small-12 medium-3 cell">
												<label>Вес
													{!! Form::number('weight', $article->weight_trans) !!}
													{{-- ['disabled' => ($article->draft == 1) ? null : true]  --}}
												</label>
										</div>
										<div class="small-12 medium-3 cell">
											<label>Единица измерения
												@include('products.articles.common.edit.select_units', [
													'field_name' => 'unit_weight_id',
													'units_category_id' => 2,
													'disabled' => null,
													'data' => $article->unit_weight_id ?? 7,
												])
											</label>
										</div>
									</div>
								</div>
							@endif

							@if($article->group->units_category_id != 5)
								<div class="cell small-12">
									<div class="grid-x grid-margin-x">
										<div class="small-12 medium-3 cell">
											<label>Объем
												{!! Form::number('volume', $article->volume_trans) !!}
											</label>
										</div>
										<div class="small-12 medium-3 cell">
											<label>Единица измерения
												@include('products.articles.common.edit.select_units', [
													'field_name' => 'unit_volume_id',
													'units_category_id' => 5,
													'disabled' => null,
													'data' => $article->unit_volume_id ?? 30,
												])
											</label>
										</div>
									</div>
								</div>
							@endif
						</div>

						{{-- Метрики --}}
						@includeIf('products.articles.'.$item->getTable().'.metrics.metrics')

                        @if ($item->getTable() == 'goods')
                            @include('products.common.edit.metrics.metrics')
                        @endif



						<div id="item-inputs"></div>
						<div class="small-12 cell tabs-margin-top text-center">
							<div class="item-error" id="item-error">Такой артикул уже существует!<br>Измените значения!</div>
						</div>
						{{ Form::hidden('item_id', $item->id) }}
					</div>
					{{-- Конец правого блока на первой вкладке --}}

					{{-- Кнопка --}}
					<div class="small-12 cell tabs-button tabs-margin-top">
						{{ Form::submit('Редактировать', ['class' => 'button', 'id' => 'add-item']) }}
					</div>

				</div>
			</div>

			{{-- Дополнительные опции --}}
			<div class="tabs-panel" id="tab-extra_options">
				<div class="grid-x grid-padding-x">
					<div class="small-12 medium-6 cell">

						<fieldset class="fieldset-access">
							<legend>Артикул</legend>

							<div class="grid-x grid-margin-x">
								<div class="small-12 medium-4 cell">
									<label id="loading">Удобный (вручную)
										{{ Form::text('manually', null, ['class' => 'check-field']) }}
										<div class="sprite-input-right find-status"></div>
										<div class="item-error">Такой артикул уже существует!</div>
									</label>
								</div>

								<div class="small-12 medium-4 cell">
									<label>Внешний
										{{ Form::text('external') }}
									</label>
								</div>

								<div class="small-12 medium-4 cell">
									<label>Программный</label>
									{{ Form::text('internal', null, ['disabled']) }}
								</div>
							</div>
						</fieldset>

	                        <fieldset class="fieldset-access">
	                            <legend>Умолчания для стоимости</legend>

	                            <div class="grid-x grid-margin-x">
	                                <div class="small-12 medium-6 cell">
	                                    <label>Себестоимость
	                                        {{ Form::number('cost_default', null) }}
	                                    </label>
	                                </div>
{{--	                                <div class="small-12 medium-6 cell">--}}
{{--	                                    <label>Цена за (<span id="unit">{{ ($article->package_status == false) ? $article->group->unit->abbreviation : 'порцию' }}</span>)--}}
{{--	                                        {{ Form::number('price_default', null) }}--}}
{{--	                                    </label>--}}
{{--	                                </div>--}}
	                            </div>
	                        </fieldset>

						@if(isset($raw))
							<fieldset class="fieldset-access">
								<legend>Умолчания для сырья</legend>

								<div class="grid-x grid-margin-x">
									<div class="small-12 medium-6 cell">
										<label>Еденица измерения
												@include('products.articles.common.edit.select_units', [
													'field_name' => 'unit_for_composition_id',
													'units_category_id' => $article->unit->category_id,
													'disabled' => null,
													'data' => $raw->unit_for_composition_id ?? $raw->article->unit_id,
												])
										</label>
									</div>
									<div class="small-12 medium-6 cell">

									</div>
								</div>
							</fieldset>
						@endif

						<fieldset class="fieldset package-fieldset" id="package-fieldset">

							<legend class="checkbox">
								{!! Form::checkbox('package_status', 1, $article->package_status, ['id' => 'package', $disabled ? 'disabled' : '']) !!}
								<label for="package">
									<span id="package-change">Сформировать порцию для приема на склад</span>
								</label>
							</legend>

							<div class="grid-x grid-margin-x" id="package-block">
								{{-- <div class="small-12 cell @if ($article->package_status == null) package-hide @endif">
									<label>Имя&nbsp;порции
										{{ Form::text('package_name', $article->package_name, ['class'=>'text-field name-field compact', 'maxlength'=>'40', 'autocomplete'=>'off', 'pattern'=>'[0-9\W\s]{0,10}', $disabled ? 'disabled' : ''], ['required']) }}
									</label>
								</div> --}}
								<div class="small-6 cell @if (!$article->package_status) package-hide @endif">
									<label>Сокр.&nbsp;имя
										{{ Form::text('package_abbreviation',  $article->package_abbreviation, ['class'=>'text-field name-field compact', 'maxlength'=>'40', 'autocomplete'=>'off', 'pattern'=>'[0-9\W\s]{0,10}', $disabled ? 'disabled' : ''], ['required']) }}
									</label>
								</div>
								<div class="small-6 cell @if (!$article->package_status) package-hide @endif">
									<label>Кол-во,&nbsp;{{ $article->unit->abbreviation }}
										{{ Form::text('package_count', $article->package_count, ['class'=>'digit-field name-field compact', 'maxlength'=>'40', 'autocomplete'=>'off', 'pattern'=>'[0-9\W\s]{0,10}', $disabled ? 'disabled' : ''], ['required']) }}
										<div class="sprite-input-right find-status" id="name-check"></div>
										<span class="form-error">Введите количество</span>
									</label>
								</div>
							</div>
						</fieldset>

						@includeIf('products.articles.'.$item->getTable().'.fieldsets')

						<fieldset class="fieldset-access">
							<legend>Доступность</legend>

                                @if ($item->archive == 1)
                                    {{-- Чекбокс архива --}}
                                    {!! Form::hidden('archive', 1) !!}
                                    {{-- @if ($article->draft) --}}
                                    <div class="small-12 cell checkbox">
                                        {!! Form::checkbox('archive', 0, $article->draft, ['id' => 'checkbox-archive']) !!}
                                        <label for="checkbox-archive"><span>Вывести из архива</span></label>
                                    </div>
                                @endif

								{{-- Чекбокс черновика --}}
								{!! Form::hidden('draft', 0) !!}
								{{-- @if ($article->draft) --}}
								<div class="small-12 cell checkbox">
									{!! Form::checkbox('draft', 1, $article->draft, ['id' => 'checkbox-draft']) !!}
									<label for="checkbox-draft"><span>Черновик</span></label>
								</div>
								{{-- @endif --}}


								<div class="small-12 cell checkbox">
									{!! Form::hidden('serial', 0) !!}
									{!! Form::checkbox('serial', 1, $item->serial, ['id' => 'checkbox-serial']) !!}
									<label for="checkbox-serial"><span>Серийный номер</span></label>
								</div>

								{{-- Чекбоксы управления --}}
								@include('includes.control.checkboxes', ['item' => $item])
								<div class="small-12 cell ">
									<span id="composition-error" class="form-error"></span>
								</div>
						</fieldset>

						<fieldset class="fieldset-access">
							<legend>Дополнительное медиа</legend>
								<label>ссылка на видео
									{{ Form::text('video_url', $article->video_url, []) }}
								</label>

{{--                                <label>Видео--}}
{{--                                    @include('includes.inputs.textarea', ['name' => 'video', 'value' => $article->video])--}}
{{--                                </label>--}}
						</fieldset>

					</div>
				</div>
			</div>




			{{-- Табы для сущности --}}
			@includeIf($pageInfo->entity->view_path . '.tabs_content')

            {{-- Сайт --}}
            @can('index', App\Site::class)
                <div class="tabs-panel" id="tab-site">
                    <div class="grid-x grid-padding-x">
                        <div class="small-12 medium-6 cell">
                            <label>Описание:
                                {{ Form::textarea('content', $article->content, ['id' => 'content-ckeditor', 'autocomplete' => 'off', 'size' => '10x3']) }}
                            </label>

                            <label>Description
                                @include('includes.inputs.textarea', ['value' => $article->seo_description, 'name' => 'seo_description'])
                            </label>

                            <label>Keywords
                                @include('includes.inputs.textarea', ['value' => $article->keywords, 'name' => 'keywords'])
                            </label>

                        </div>
                    </div>
                </div>
            @endcan

			{{ Form::close() }}



			{{-- Фотографии --}}
			<div class="tabs-panel" id="tab-photos">
				<div class="grid-x grid-padding-x">

					<div class="small-12 medium-7 cell">

						{!!  Form::open([
							'route' => 'photos.ajax_store',
							'data-abide',
							'novalidate',
							'files' => 'true',
							'class' => 'dropzone',
							'id' => 'my-dropzone'
						]
						) !!}

						{!! Form::hidden('name', $article->name) !!}
						{!! Form::hidden('id', $article->id) !!}
						{!! Form::hidden('entity', 'articles') !!}
						{!! Form::hidden('album_id', $item->album_id) !!}

						{!! Form::close() !!}
{{--							<dropzone-component :dropzone="{{ $dropzone }}"></dropzone-component>--}}

						<ul class="grid-x small-up-4 tabs-margin-top" id="photos-list">

							@isset($article->album_id)
								@include('photos.photos', ['album' => $article->album])
							@endisset

						</ul>
					</div>

					<div class="small-12 medium-5 cell" id="photo-edit-partail">

						{{-- Форма редактированя фотки --}}

					</div>
				</div>

			</div>

		</div>
	</div>
</div>
@endsection

@section('modals')
{{--	@include('includes.modals.modal_item_delete')--}}
	@includeIf($pageInfo->entity->view_path . '.modals')
@endsection

@push('scripts')
	<script>
		// Основные настройки
		var category_entity = '{{ $category_entity }}';

		// При клике на фотку подствляем ее значения в блок редактирования
		$(document).on('click', '#photos-list .edit', function(event) {
			event.preventDefault();

			// Удаляем всем фоткам активынй класс
			$('#photos-list img').removeClass('active');
			$('#photos-list img').removeClass('updated');

			// Наваливаем его текущей
			$(this).addClass('active');

			// Получаем инфу фотки
			$.post('/admin/photo_edit/' + $(this).data('id'), function(html){
				// alert(html);
				$('#photo-edit-partail').html(html);
			})
		});

		// При сохранении информации фотки
		$(document).on('click', '#form-photo-edit .button-photo-edit', function(event) {
			event.preventDefault();

			let button = $(this);
			button.prop('disabled', true);

			let id = $(this).closest('#form-photo-edit').find('input[name=id]').val();
			// alert(id);

			// Записываем инфу и обновляем
			$.ajax({
				url: '/admin/photo_update/' + id,
				type: 'PATCH',
				data: $(this).closest('#form-photo-edit').serialize(),
				success: function(res) {

					if (res == true) {
						button.prop('disabled', false);

						$('#photos-list').find('.active').addClass('updated').removeClass('active');
					} else {
						alert(res);
					};
				}
			})
		});

		// При сохранении удалении фотки
		$(document).on('click', '#form-photo-edit .button-delete-photo', function(event) {
			event.preventDefault();

			let button = $(this);
			button.prop('disabled', true);

			let id = $(this).data('photo-id');
			// alert(id);

			// Записываем инфу и обновляем
			$.ajax({
				url: '/admin/photo_delete/' + id,
				type: 'DELETE',
				success: function(html) {
					$('#photos-list').html(html);
					$('#photo-edit-partail').html('');
				}
			})
		});
	</script>

{{--	@include('products.articles.common.edit.change_articles_groups_script')--}}
	@include('products.articles.common.edit.change_packages_script', [
		'package_unit' => $article->unit->abbreviation
	])

	@include('includes.scripts.inputs-mask')
	@include('includes.scripts.upload-file')
    @include('includes.scripts.ckeditor')

	@include('includes.scripts.dropzone', [
		'settings' => $settings,
		'item_id' => $article->id,
		'item_entity' => 'articles'
	])

	{{-- Проверка поля на существование --}}
	@include('includes.scripts.check', [
		'entity' => 'articles',
		'id' => $article->id
	])

{{--	@includeIf($pageInfo->entity->view_path . '.scripts')--}}
@endpush
