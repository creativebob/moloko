<div class="grid-x grid-padding-x">
    <aside class="cell small-12 medium-5 large-3 sidebar" data-sticky-container>
        <div class="sticky" data-sticky data-sticky-on="medium" data-top-anchor="278" data-btm-anchor="wrap-sidebar:bottom" data-margin-top="2">
			@include('project.composers.catalogs_services.sidebar')
		</div>
	</aside>
	<main class="cell small-12 medium-7 large-9 main-content">
		<article class="page-content">
			<div class="grid-x grid-padding-x">
				<div class="cell small-12">
					{{-- Заголовок --}}
					@include('viandiesel.pages.common.title')
				</div>
				<div class="cell small-12 large-7">
					{!! $page->content !!}
				</div>
				<div class="cell small-12 large-5">

					{{-- Блок отображения альбома --}}
					{{-- @isset($price_goods->goods->article->album)
	                    <ul class="grid-x small-up-2 medium-up-2 large-up-3 album-list gallery">
	                        @foreach($price_goods->goods->article->album->photos->take(4) as $photo)
	                            <li class="cell">
	                                <a data-fancybox="gallery" href="{{ getPhotoInAlbumPath($photo, 'large') }}">
	                                    <img src="{{ getPhotoInAlbumPath($photo) }}" alt="{{ $photo->name ?? '' }}"
	                                         class="thumbnail">
	                                    <span class="tool-search"></span>
	                                </a>
	                            </li>
	                        @endforeach
	                    </ul>
	                @endisset --}}

                    @include('project.composers.albums.album_by_alias', ['albumAlias' => 'services'])

				</div>
			</div>
		</article>
	</main>
</div>
