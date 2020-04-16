		@isset($catalogs_services_items)
			<ul class="cell menu main-menu vertical medium-horizontal">
				@foreach($catalogs_services_items as $item)
					@if($item->slug == $page->alias)
						<li class="is-active"><span class="isactive-item" >{{ $item->name }}</span></li>
					@else
						<li><a href="/{{ $item->slug }}">{{ $item->name }}</a></li>
					@endif
				@endforeach
			</ul>
		@endisset