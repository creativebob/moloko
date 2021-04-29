<div class="grid-x grid-padding-x">

    @include('project.composers.catalogs_services.sidebar')

    <main class="cell small-12 medium-7 large-9 main-content">

        {{-- Заголовок --}}
        @include('viandiesel.pages.common.title')

        <prices-goods-component :prices-goods='@json($prices_goods)'></prices-goods-component>

    </main>
</div>
