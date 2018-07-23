@if (isset($sidebar['children']))
<li><a data-link="{{ $sidebar['id'] }}"><span>{{ $sidebar['name'] }}</span></a>
	  @if (isset($sidebar['children']))
	    <ul class="menu vertical nested">
	      @foreach($sidebar['children'] as $sidebar)
	        @include('layouts.sidebar-list', $sidebar)
	      @endforeach
	       </ul>
	    @endif
	</li>
	
@else
	{{-- Если конечный пункт --}}
	<li>
		<a href="/{{ $sidebar['alias'] }}" data-link="{{ $sidebar['id'] }}">{{ $sidebar['name'] }}</a>
	</li>
@endif

 








 

         