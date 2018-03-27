@if (isset($menu['children']))
<li class="medium-item item @if (isset($navigation['menus'])) parent @endif" id="menus-{{ $menu['id'] }}" data-name="{{ $menu['menu_name'] }}">
	<a class="medium-link">
		<div class="icon-open sprite"></div>
		<span>{{ $menu['menu_name'] }}</span>
		<span class="number">
			@if (isset($menu['children']))
			{{ count($menu['children']) }}
			@else
			0
			@endif
		</span>
		@if ($menu['moderation'])
		<span class="no-moderation">Не отмодерированная запись!</span>
		@endif
		@if ($menu['system_item'])
		<span class="system-item">Системная запись!</span>
		@endif
	</a>
	<div class="drop-list checkbox">
		@if ($drop == 1)
		<div class="sprite icon-drop"></div>
		@endif
		<input type="checkbox" name="" id="check-{{ $menu['id'] }}">
		<label class="label-check" for="check-{{ $menu['id'] }}"></label> 
	</div>
	<div class="icon-list">
		<div>
			@can('create', App\Menu::class)
			<div class="icon-list-add sprite" data-open="medium-add"></div>
			@endcan
		</div>
		<div>
			{{-- @if($menu['edit'] == 1) --}}
			<div class="icon-list-edit sprite" data-open="medium-edit"></div>
			{{-- @endif --}}
		</div>
		<div class="del">
			@if(($menu['system_item'] != 1) && ($menu['delete'] == 1))
			<div class="icon-list-delete sprite" data-open="item-delete-ajax"></div>
			@endif
		</div>
	</div>
	<ul class="menu vertical medium-list nested" data-accordion-menu data-multi-open="false">
		@if(isset($menu['children']))
		@foreach($menu['children'] as $menu)
		@include('navigations.menus-list', $menu)
		@endforeach
		@else
		<li class="empty-item"></li>
		@endif
	</ul>
</li>
@else
{{-- Конечный --}}
<li class="medium-as-last item" id="menus-{{ $menu['id'] }}" data-name="{{ $menu['menu_name'] }}">
	<a class="medium-as-last-link">
		<span>{{ $menu['menu_name'] }}</span>
		@if ($menu['moderation'])
		<span class="no-moderation">Не отмодерированная запись!</span>
		@endif
		@if ($menu['system_item'])
		<span class="system-item">Системная запись!</span>
		@endif
	</a>
	<div class="icon-list">
		<div>
			@can('create', App\Menu::class)
			<div class="icon-list-add sprite" data-open="medium-add"></div>
			@endcan
		</div>
		<div>
			{{-- @if($menu['edit'] == 1) --}}
			<div class="icon-list-edit sprite" data-open="medium-edit"></div>
			{{-- @endif --}}
		</div>
		<div class="del">
			@if(($menu['system_item'] != 1) && ($menu['delete'] == 1))
			<div class="icon-list-delete sprite" data-open="item-delete-ajax"></div>
			@endif
		</div>
	</div>
	<div class="drop-list checkbox">
		@if ($drop == 1)
		<div class="sprite icon-drop"></div>
		@endif
		<input type="checkbox" name="" id="menu-check-{{ $menu['id'] }}">
		<label class="label-check" for="menu-check-{{ $menu['id'] }}"></label> 
	</div>
</li>
@endif












