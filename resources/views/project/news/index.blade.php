@extends('project.layouts.app')

@section('title')
<title>{{ $page->title }} | Воротная компания "Марс"</title>
<meta name="description" content="{{ $page->description }} ">
@endsection

@section('content')
<div class="wrap-main grid-x">
	<div class="cell small-12 medium-12 large-12" data-sticky-container  id="foo2">
		<h1 data-sticky data-top-anchor="foo2:top" data-options="marginTop:1.6;">Наши новости<a href="#anchor-menu" title="Наверх!"><span class="arrow-top"></span></a></h1>
	</div>

	<main class="cell small-12 medium-12 large-12 news">
		@if (isset($content))
		<ul class="list-news">
			@foreach ($content as $news)
			<li>
				<article class="media-object stack-for-small" itemscope itemtype="http://schema.org/NewsArticle">
					@if (isset($news->photo_id))
					<div class="media-object-section">
						<a data-fancybox="news" href="/storage/{{ $news->company_id }}/media/news/{{ $news->id }}/img/large/{{ $news->photo->name }}">
							<img class="thumbnail" src="/storage/{{ $news->company_id }}/media/news/{{ $news->id }}/img/small/{{ $news->photo->name }}" alt="{{ $news->photo->name }}"><span class="tool-search"></span>
						</a>
					</div>
					@else
					Нет превью
					@endif
					<div class="media-object-section right-part-news">
						<div class="grid-x">
							<div class="cell small-12 medium-8 large-8">
								<h2 itemprop="name">{{ $news->title }}</h2>
								<p itemprop="description">{{ $news->preview }}</p>
								<p class="date-desc">
									<span class="date-public"><time itemprop="datePublished" datetime="2016-10-12">{{ $news->created_at->format('d.m.Y') }}</time></span>
									@if (!empty($news->content))
									<a href="/news/{{ $news->alias }}" class="read-more" itemprop="url">Читать</a>
									@endif
								</p>
							</div>
							<div class="cell small-12 medium-4 large-4">
								<div class="grid-x">
									<div class="cell small-4 medium-12 large-12 img-personal">
										@if (isset($news->author->photo_id))
										 	<img src="/storage/{{ $news->author->company_id }}/media/users/{{ $news->author_id }}/img/original/{{ $news->author->avatar->name }}" alt="{{ $news->author->first_name . ' ' . $news->author->second_name }}">
										@else
											Нет фото
										@endif
									</div>
									<div class="cell small-8 medium-12 large-12">
										<span class="name-personal">{{ $news->author->first_name . ' ' . $news->author->second_name }}</span>

										<span class="post-personal">
											@if ($news->author->staff)
											{{ $news->author->staff[0]->position->name }}
											@else
											Сотрудник
											@endif
										</span>
									</div>
								</div>
							</div>
						</div>
					</div>
				</article>
			</li>

			@endforeach
		</ul>
		@endif
	</main>
</div>
@endsection