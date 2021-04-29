<div class="grid-x grid-padding-x">
	@include('project.composers.tools_categories.sidebar_with_items')
	<main class="cell small-12 medium-7 large-9 main-content">
		<article class="page-content">
			<div class="grid-x">
				<div class="cell small-12 medium-7">
					{{-- Заголовок --}}
					@include('viandiesel.pages.common.title')

					{!! $page->content !!}

				</div>
			</div>
		</article>
	</main>
</div>
