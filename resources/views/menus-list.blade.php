@if (isset($menu['page']))
	{{-- Ссылка на страничку --}}
	<li class="medium-item parent" id="menus-{{ $menu['id'] }}" data-name="{{ $menu['page']['page_name'] }}">
		<div class="medium-as-last">{{ $menu['page']['page_name'] }}
		  <ul class="icon-list">
		      <li><div class="icon-list-edit sprite" data-open="menu-edit"></div></li>
		      <li>
		        @if (!isset($menu['children']))
		          <div class="icon-list-delete sprite" data-open="item-delete"></div>
		        @endif
		      </li>
		  </ul>
		</div>
	</li>
@else
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
	  	<li><div class="icon-list-add sprite" data-open="menu-add"></div></li>
		  <li><div class="icon-list-edit sprite" data-open="menu-edit"></div></li>
	    <li>
	    @if(!isset($menu['children']))
	      <div class="icon-list-delete sprite" data-open="item-delete"></div>
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
@endif


  







 

         