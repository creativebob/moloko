		@php if(!isset($align)){$align = 'top';} @endphp
		@isset($navigations[$align])
			<ul class="cell menu vertical medium-horizontal {{ $align }}-menu">
				@foreach($navigations[$align]->first()->menus as $menu)
					@if($menu->page->alias == $page->alias)
						<li class="is-active"><span class="isactive-item" >{{ $menu->name }}</span></li>
					@else
						<li><a href="/{{ $menu->page->alias }}">{{ $menu->name }}</a></li>
					@endif
				@endforeach
			</ul>
		@endisset