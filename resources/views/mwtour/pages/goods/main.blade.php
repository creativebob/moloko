<div class="grid-x grid-padding-x">
	@include('project.composers.catalogs_goods.sidebar')
	<main class="cell small-12 medium-7 large-9 main-content">
		<article class="page-content">
			<div class="grid-x">
				<div class="cell small-12">
					{{-- Заголовок --}}
					@include('viandiesel.pages.common.title')

			        <div class="cell small-12">
			            <search-component></search-component>
			        </div>

					{!! $page->content !!}

				</div>
			</div>
		</article>
	</main>
</div>