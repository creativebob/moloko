@if (isset($menu['children']))
	<li class="medium-item item @if (isset($navigation['menus'])) parent @endif" id="menus-{{ $menu['id'] }}" data-name="{{ $menu['menu_name'] }}">
		<ul class="icon-list">
	  	<li>
	  		@can('create', App\Menu::class)
                <div class="icon-list-add sprite" data-open="menu-add"></div>
            @endcan
	  	</li>
			<li>
				@if($menu['edit'] == 1)
				<div class="icon-list-edit sprite" data-open="menu-edit"></div>
				@endif
			</li>
	    <li>
	    @if(($navigation['system_item'] != 1) && (!isset($menu['children'])) && ($menu['delete'] == 1))
	      <div class="icon-list-delete sprite" data-open="item-delete"></div>
	    @endif
	    </li>
	  </ul>
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
	  <div class="drop-list checkbox">
      @if ($drop == 1)
      <div class="sprite icon-drop"></div>
      @endif
      <input type="checkbox" name="" id="check-{{ $menu['id'] }}">
      <label class="label-check" for="check-{{ $menu['id'] }}"></label> 
    </div>
	  @if(isset($menu['children']))
     	<ul class="menu vertical medium-list accordion-menu nested" data-accordion-menu data-allow-all-closed data-multi-open="false">
	      @foreach($menu['children'] as $menu)
	        @include('navigations.navigations-list', $menu)
	      @endforeach
	    </ul>
    @endif
	</li>
@else
	{{-- Конечный --}}
	<li class="medium-item item" id="menus-{{ $menu['id'] }}" data-name="{{ $menu['menu_name'] }}">
		<div class="medium-as-last">{{ $menu['menu_name'] }}
		  <ul class="icon-list">
		  	<li>
		  		@can('create', App\Menu::class)
	                <div class="icon-list-add sprite" data-open="menu-add"></div>
	            @endcan
		  	</li>
				<li>
					@if($menu['edit'] == 1)
					<div class="icon-list-edit sprite" data-open="menu-edit"></div>
					@endif
				</li>
		    <li>
		    @if(($navigation['system_item'] != 1) && (!isset($menu['children'])) && ($menu['delete'] == 1))
		      <div class="icon-list-delete sprite" data-open="item-delete"></div>
		    @endif
		    </li>
		  </ul>
		</div>
		<div class="drop-list checkbox">
      @if ($drop == 1)
      <div class="sprite icon-drop"></div>
      @endif
      <input type="checkbox" name="" id="check-{{ $menu['id'] }}">
      <label class="label-check" for="check-{{ $menu['id'] }}"></label> 
    </div>
	</li>

@endif


  







 

         