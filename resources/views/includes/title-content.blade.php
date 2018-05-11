{{-- Заголовок и фильтры --}}
<div data-sticky-container id="head-content">
	<div class="sticky sticky-topbar" id="head-sticky" data-sticky data-margin-top="2.4" data-sticky-on="small" data-top-anchor="head-content:top">
		<div class="top-bar head-content">
			<div class="top-bar-left">
				
				@can('create', $class)
				@switch($type)

				@case('table')
				@if (isset($page_alias))
				<h2 class="header-content">{{ $page_info->title }}</h2>
				<a href="/{{ $page_alias }}/create" class="icon-add sprite"></a>
				@else
				<h2 class="header-content">{{ $page_info->title }}</h2>
				<a href="/{{ $page_info->alias}}/create" class="icon-add sprite"></a>
				@endif
				@break

				@case('section-table')
				<h2 class="header-content">{{ $page_info->title .' &laquo;'. $name .'&raquo;' }}</h2>
				<a href="/{{ $page_alias }}/create" class="icon-add sprite"></a>
				@break

				@case('menu')
				<h2 class="header-content">{{ $page_info->title }}</h2>
				<a class="icon-add sprite" data-open="first-add"></a>
				@break

				@case('sections-menu')
				<h2 class="header-content">{{ $page_info->title .' &laquo;'. $name .'&raquo;' }}</h2>
				<a class="icon-add sprite" data-open="first-add"></a>
				@break

				@endswitch
				@endcan
			</div>
			<div class="top-bar-right">
				@if (isset($filter))
				<a class="icon-filter sprite @if ($filter['status'] == 'active') filtration-active @endif"></a>
				@endif
				<input class="search-field" type="search" name="search_field" placeholder="Поиск" />
				<button type="button" class="icon-search sprite button"></button>
			</div>
		</div>
		{{-- Блок фильтров --}}
		@if (isset($filter))

			{{-- Подключаем класс Checkboxer --}}
			@include('includes.scripts.class.checkboxer')

			<div class="grid-x">
				<div class="small-12 cell filters fieldset-filters" id="filters">
					<div class="grid-padding-x">
						<div class="small-12 cell text-right">
							{{ link_to_route($page_info->alias .'.index', 'Сбросить', $value = Null, ['class' => 'small-link']) }}
						</div>
					</div>
					<div class="grid-padding-x">
						<div class="small-12 cell">
						{{ Form::open(['url' => $page_info->alias, 'data-abide', 'novalidate', 'name'=>'filter', 'method'=>'GET', 'id' => 'filter-form', 'class' => 'grid-x grid-padding-x inputs']) }}

							@include($page_info->alias.'.filters')

							<div class="small-12 cell text-center">
								{{ Form::submit('Фильтрация', ['class'=>'button']) }}
							</div>
							{{ Form::close() }}
						</div>
					</div>
					<div class="grid-x">
						<a class="small-12 cell text-center filter-close">
							<button type="button" class="icon-moveup sprite"></button>
						</a>
					</div>
				</div>
			</div>

		@endif
	</div>
</div>