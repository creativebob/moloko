@if (isset($sidebar['page']))
	{{-- Если вложенный --}}
	<li>
		<a href="{{ $sidebar['page']['page_alias'] }}" data-link="{{ $sidebar['id'] }}">{{ $sidebar['page']['page_name'] }}</a>
	</li>
@else
	<li><a data-link="{{ $sidebar['id'] }}"><span>{{ $sidebar['menu_name'] }}</span></a>
	  @if (isset($sidebar['children']))
	    <ul class="menu vertical nested">
	      @foreach($sidebar['children'] as $sidebar)
	        @include('includes.sidebar-list', $sidebar)
	      @endforeach
	       </ul>
	    @endif
	 
	</li>
@endif

 








 

         