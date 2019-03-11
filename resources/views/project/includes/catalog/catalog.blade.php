<aside class="small-11 medium-2 cell sidebar" id="sidebar" data-sticky-container>
	<div class="sticky" data-sticky data-top-anchor="sidebar:top" data-stick-to="top" data-sticky-on="medium" data-margin-top="8" style="width:100%;">

		<h3>{{ $catalog->name }}</h3>

        @isset ($catalog->items)
        <ul class="vertical menu" data-accordion-menu data-multi-open="false">
			@include('project.includes.catalog.catalogs_items', ['catalogs_items' => $catalog->items])
		</ul>

        @endisset

	</div>
</aside>


