{{-- Таблица --}}
<div data-sticky-container id="head-content">
	<div class="sticky sticky-topbar" id="head-sticky" data-sticky-on="small" data-sticky data-margin-top="2.4" data-top-anchor="head-content:top">
		<div class="top-bar head-content">
			<div class="top-bar-left">
				<h2 class="header-content">{{ $page_info->page_name }}</h2>
				@can('create', $class)
				<a href="/{{ $page_info->page_alias}}/create" class="icon-add sprite"></a>
				@endcan
			</div>
			<div class="top-bar-right">
				<a class="icon-filter sprite"></a>
				<input class="search-field" type="search" name="search_field" placeholder="Поиск" />
				<button type="button" class="icon-search sprite button"></button>
			</div>
		</div>
		{{-- Блок фильтров --}}
		<div class="grid-x">
			<div class="small-12 cell filters" id="filters">
				<fieldset class="fieldset-filters inputs">
					@include($page_info->page_alias.'.filters')
				</fieldset>
			</div>
		</div>
	</div>
</div>