<!-- noindex -->
<div class="grid-x grid-padding-x">
    <main class="cell small-12">

        {{-- Заголовок --}}
        @include('viandiesel.pages.common.title')

        <div class="grid-x grid-padding-x">
            <div class="cell small-12 medium-6 page-content">
                {!! $page->content !!}
                <p>Вы подписаны!</p>
            </div>
        </div>
    </main>
</div>
<!-- /noindex -->
