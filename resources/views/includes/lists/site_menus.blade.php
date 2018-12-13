<ul>
	@foreach ($menus as $menu)
	<li class="checkbox">
		{{ Form::checkbox('menus[]', $menu->id, null, ['id'=>'menu-'.$menu->id]) }}
		<label for="menu-{{ $menu->id }}"><span>{{ $menu->name }}</span></label>
	</li>
	@endforeach
</ul>