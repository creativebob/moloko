<ul>
	@foreach ($sites as $site)
	<li class="checkbox">
		{{ Form::checkbox('sites[]', $site->id, null, ['id'=>'site-'.$site->id]) }}
		<label for="site-{{ $site->id }}"><span>{{ $site->name }}</span></label>
	</li>
	@endforeach
</ul>