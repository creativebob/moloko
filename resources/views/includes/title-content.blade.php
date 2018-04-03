{{-- Заголовок и фильтры --}}
<div data-sticky-container id="head-content">
	<div class="sticky sticky-topbar" id="head-sticky" data-sticky data-margin-top="2.4" data-sticky-on="small" data-top-anchor="head-content:top">
		<div class="top-bar head-content">
			<div class="top-bar-left">
				<h2 class="header-content">{{ $page_info->page_name }}</h2>
				@can('create', $class)

				@switch($type)
				@case('table')
				@if (isset($page_alias))
				<a href="/{{ $page_alias}}/create" class="icon-add sprite"></a>
				@else
				<a href="/{{ $page_info->page_alias}}/create" class="icon-add sprite"></a>
				@endif
				@break

				@case('menu')
				<a class="icon-add sprite" data-open="first-add"></a>
				@break
				@endswitch

				@endcan
			</div>
			<div class="top-bar-right">
				{{-- @if ($filter['status'] == 'active') filtration-active @endif --}}
				<a class="icon-filter sprite"></a>
				<input class="search-field" type="search" name="search_field" placeholder="Поиск" />
				<button type="button" class="icon-search sprite button"></button>
			</div>
		</div>
		{{-- Блок фильтров --}}
		<div class="grid-x">
			<div class="small-12 cell filters fieldset-filters" id="filters">

				{{ Form::open(['url' => $page_info->page_alias, 'data-abide', 'novalidate', 'name'=>'filter', 'method'=>'GET', 'class' => 'grid-x grid-padding-x inputs']) }}
				{{-- Подключаем класс Checkboxer --}}
				@include('includes.scripts.class.checkboxer')
				@include($page_info->page_alias.'.filters')
				<div class="small-12 medium-6 cell text-left">
					{{ Form::submit('Фильтрация', ['class'=>'button']) }}
				</div>
				<div class="small-12 medium-6 cell text-right">
					{{ Form::submit('Сбросить', ['url' => $page_info->page_alias, 'class'=>'button']) }}
				</div>
				{{ Form::close() }}

				<div class="grid-x">
					<a class="small-12 cell text-center filter-close">стрелка</a>
				</div>

			</div>
		</div>
	</div>
</div>