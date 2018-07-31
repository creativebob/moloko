	
	@if(!empty($result_search))
	<div class="small-12 medium-6 cell" id="search-result-wrap">
		<h3 class="search-result-head">Вот, что удалось найти:</h3>
		<ul class="search-result-list">
			@foreach($result_search as $item)
			<li><a href="">{{ $item->name }}</a></li>
			@endforeach
		</ul>
	</div>
	@endif
