@if (isset($menu['children']))
	<li class="medium-item parent" id="menus-{{ $menu['id'] }}" data-name="{{ $menu['menu_name'] }}">
	  <a class="medium-link">
	    <div class="list-title">
	      <div class="icon-open sprite"></div>
	      <span>{{ $menu['menu_name'] }}</span>
	      <span class="number">
	      @if (isset($menu['children']))
           {{ count($menu['children']) }}
          @else
            0
          @endif
        </span>
	    </div>
	  </a>
	  <ul class="icon-list">
	  	<li>
	  		@can('create', App\Menu::class)
                <div class="icon-list-add sprite" data-open="menu-add"></div>
            @endcan
	  	</li>
		<li>
			@can('update', collect($menu))
			<div class="icon-list-edit sprite" data-open="menu-edit"></div>
			@endcan
		</li>
	    <li>
	    @if(($navigation['system_item'] != 1) && (!isset($menu['children'])))
	    	@can('delete', collect($menu))
	      <div class="icon-list-delete sprite" data-open="item-delete"></div>
	       @endcan
	    @endif
	    </li>
	  </ul>
	  @if(isset($menu['children']))
     	<ul class="menu vertical medium-list accordion-menu nested" data-accordion-menu data-allow-all-closed data-multi-open="false">
	      @foreach($menu['children'] as $menu)
	        @include('menus-list', $menu)
	      @endforeach
	    </ul>
    @endif
	</li>
@else
	{{-- Конечный --}}
	<li class="medium-item" id="menus-{{ $menu['id'] }}" data-name="{{ $menu['menu_name'] }}">
		<div class="medium-as-last">{{ $menu['menu_name'] }}
		  <ul class="icon-list">
		  	<li>
		  		@can('create', App\Menu::class)
	                <div class="icon-list-add sprite" data-open="menu-add"></div>
	            @endcan
		  	</li>
			<li>
				@can('update', collect($menu))
				<div class="icon-list-edit sprite" data-open="menu-edit"></div>
				@endcan
			</li>
		    <li>
		    @if(($navigation['system_item'] != 1) && (!isset($menu['children'])))
		    	@can('delete', collect($menu))
		      <div class="icon-list-delete sprite" data-open="item-delete"></div>
		       @endcan
		    @endif
		    </li>
		  </ul>
		</div>
	</li>

@endif


  







 

         