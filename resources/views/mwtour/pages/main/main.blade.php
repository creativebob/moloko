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

        {{-- @include('project.composers.albums.album_by_alias', ['albumAlias' => 'main-album']) --}}

        <ul class="grid-x small-up-3 medium-up-4 large-up-6 album-list gallery">
            <li class="cell">
                <a data-fancybox="gallery" href="/img/mwtour/album/1.jpg">
                    <img src="/img/mwtour/album/1.jpg"
                         class="thumbnail" width="300" height="199" alt="">
                    <span class="tool-search"></span>
                </a>
            </li>
            <li class="cell">
                <a data-fancybox="gallery" href="/img/mwtour/album/1.jpg">
                    <img src="/img/mwtour/album/2.jpg"
                         class="thumbnail" width="300" height="199" alt="">
                    <span class="tool-search"></span>
                </a>
            </li>
            <li class="cell">
                <a data-fancybox="gallery" href="/img/mwtour/album/1.jpg">
                    <img src="/img/mwtour/album/3.jpg"
                         class="thumbnail" width="300" height="199" alt="">
                    <span class="tool-search"></span>
                </a>
            </li>
            <li class="cell">
                <a data-fancybox="gallery" href="/img/mwtour/album/1.jpg">
                    <img src="/img/mwtour/album/4.jpg"
                         class="thumbnail" width="300" height="199" alt="">
                    <span class="tool-search"></span>
                </a>
            </li>
            <li class="cell">
                <a data-fancybox="gallery" href="/img/mwtour/album/1.jpg">
                    <img src="/img/mwtour/album/5.jpg"
                         class="thumbnail" width="300" height="199" alt="">
                    <span class="tool-search"></span>
                </a>
            </li>
            <li class="cell">
                <a data-fancybox="gallery" href="/img/mwtour/album/1.jpg">
                    <img src="/img/mwtour/album/6.jpg"
                         class="thumbnail" width="300" height="199" alt="">
                    <span class="tool-search"></span>
                </a>
            </li>
        </ul>

        {{-- Форма подписки --}}
        @include('project.pages.forms.subscribe', ['title' => 'Подпишись на оповещения о новых турах!'])

    </main>
</div>

@push('scripts')
    <script src="/js/plugins/fancybox/dist/jquery.fancybox.min.js"></script>
@endpush
