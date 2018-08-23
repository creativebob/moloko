@extends('project.layouts.app')

@section('title', 'Кабинет')

@section('content')

{{-- Заголовок страницы --}}
<div class="grid-x align-center cabinet">
  <div class="small-11 medium-10 cell text-left">
  	
  	@if (isset($album))
    <div class="orbit" role="region" aria-label="Favorite Space Pictures" data-orbit>
      <div class="orbit-wrapper">
        <ul class="orbit-container">

          @if ((count($album->photos) > 0))
          @foreach ($album->photos as $photo)
          <li class="orbit-slide">
            <figure class="orbit-figure">
              <img class="orbit-image thumbnail" src="/storage/{{ $photo->company_id }}/media/albums/{{ $photo->album_id }}/img/original/{{ $photo->name }}" width="{{ $album->album_settings->img_min_width }}" height="{{ $album->album_settings->img_min_height }}" alt="{{ $photo->title }}">
            </figure>
            @if (isset($photo->description))
            <div class="img-desc" style="background-color: {{ $photo->color }}">{{ $photo->description }}</div>
            @endif
          </li>
          @endforeach
        </ul>
      </div>
      <nav class="orbit-bullets">
        @for ($i = 0; $i < count($album->photos); $i++)
        <button data-slide="{{ $i }}" @if ($i == 0) class=is-active @endif></button>
        @endfor
        @endif
      </nav>
    </div>
    @endif
  </div>
</div>

{{-- Контент --}}
<main class="grid-x align-center">
  <div class="small-11 medium-10 cell">

    @if (isset($catalogs_tree))
    <div class="grid-x small-up-1 medium-up-3 services">

      @foreach ($catalogs_tree as $catalog)
      <div class="cell">
        @if($catalog['category_status'] == 1)
        <h2 class="head-item-nav">{{ $catalog['name'] }}</h2>
        @if (count($catalog['children']) > 0)
        <ul class="menu vertical">
          @foreach ($catalog['children'] as $children)
          <li><a href="/services/{{ $children['id'] }}">{{ $children['name'] }}</a></li>
          @endforeach
        </ul>
        @endif
        @endif
      </div>
      @endforeach
    </div>
    @endif
  </div>
</main>

@endsection

@section('scripts')

@endsection