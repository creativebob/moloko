<!-- noindex -->
<div class="grid-x grid-padding-x">
    <main class="cell small-12">

        {{-- Заголовок --}}
        @include('mwtour.pages.common.title')

        <div class="grid-x grid-padding-x">
            <div class="cell small-12 medium-6 page-content">
                {!! $page->content !!}
                <h2>Вы подписаны!</h2>
            </div>
        </div>
    </main>
</div>
<!-- /noindex -->
