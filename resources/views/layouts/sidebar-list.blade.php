@if (isset($sidebar['children']))
<li><a data-link="{{ $sidebar['id'] }}"><span>{{ $sidebar['menu_name'] }}</span></a>
	  @if (isset($sidebar['children']))
	    <ul class="menu vertical nested">
	      @foreach($sidebar['children'] as $sidebar)
	        @include('includes.sidebar-list', $sidebar)
	      @endforeach
	       </ul>
	    @endif
	</li>
	
@else
	{{-- Если конечный пункт --}}
	<li>
		<a href="/{{ $sidebar['menu_alias'] }}" data-link="{{ $sidebar['id'] }}">{{ $sidebar['menu_name'] }}</a>
	</li>
@endif

 








 

         