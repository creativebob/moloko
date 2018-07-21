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
		<a href="{{ $sidebar['alias'] }}" data-link="{{ $sidebar['id'] }}">{{ $sidebar['name'] }}</a>
		{{-- link_to($sidebar['alias'], $title = $sidebar['name'], $attributes = ['data-link' => $sidebar['id']], $secure = null) --}}
	</li>
@endif

 








 

         