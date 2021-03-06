<div class="grid-x grid-padding-x">

    <main class="cell small-12 main-content">

        <div class="grid-x">
            <div class="cell small-12 medium-12 large-8 wrap-main-content">
               <span class="subtitle">{{ $page->subtitle }}</span>
               {!! $page->content !!}
               <span class="text-slogan">Живи, твори, мечтай!</span>
            </div>

            <div class="cell small-12 medium-12 large-4 wrap-team">
                {{-- Сотрудники --}}
                @include('project.composers.staff.section')
            </div>
        </div>

        {{-- Слайдер с продвижениями --}}
        @include('project.composers.promotions.slider')

        {{-- Туры --}}

        <h1 class="text-center h1-main-page">{{ $page->seo->h1 ?? $page->name }}</h1>
        @include('project.composers.services.section')

        {{-- @if($page->video)
            <div class="grid-x">
                <div class="cell small-12 wrap-video-block">
                    <div class="video-wrap">
                        {!! $page->video !!}
                    </div>
                </div>
            </div>
        @endif --}}

        @include('project.composers.albums.album_by_alias', ['albumAlias' => 'main-album'])

        {{-- Отзывы --}}
{{--        @include('project.composers.feedbacks.section')--}}

        {{-- Форма подписки --}}
        @include('project.pages.forms.subscribe', ['title' => 'Подпишись на оповещения о новых турах!'])

    </main>
</div>

@push('scripts')
    <script src="/js/plugins/fancybox/dist/jquery.fancybox.min.js"></script>
@endpush
