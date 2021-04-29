<div class="grid-x grid-padding-x">
	@include('project.composers.tools_categories.sidebar_with_items')
	<main class="cell small-12 medium-7 large-9 main-content">
		<article class="page-content">
			<div class="grid-x grid-padding-x">
				<div class="cell small-12 medium-7">

					<h1>{{ $tool->article->name }}</h1>
					@if(!empty($page->subtitle))<span>{{ $page->subtitle }}</span>@endif

					<div class="grid-x wrap-tool">
						<div class="cell small-12 tool-img">
							<img src="{{ getPhotoPathPlugEntity($tool) }}" alt="{{ $tool->article->name }}" 
							@if(!empty($tool->article->photo))
								width="440" 
								height="292" 
							@endif
							>
						</div>
						<div class="cell small-12 tool-content page-content">
							{!! $tool->article->content !!}
						</div>
					</div>
				</div>
			</div>
		</article>
	</main>
</div>