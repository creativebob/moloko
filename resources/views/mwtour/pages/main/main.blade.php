<div class="grid-x grid-padding-x">

    <aside class="cell small-12 medium-5 large-3 sidebar" data-sticky-container>
        <div class="sticky" data-sticky data-sticky-on="medium" data-top-anchor="278" data-btm-anchor="wrap-sidebar:bottom" data-margin-top="2">
            @include('project.composers.catalogs_services.sidebar')
            @include('project.composers.news.images')
        </div>
    </aside>

    <main class="cell small-12 medium-7 large-9 main-content">

        {{-- Заголовок --}}
        @include('viandiesel.pages.common.title')

        @include('project.composers.promotions.slider')

        <div class="grid-x grid-padding-x main-content">
            <div class="cell small-12 medium-12 large-7">
                <div class="page-content">
                    {!! $page->content !!}
                </div>
            </div>

            <div class="cell small-12 medium-12 large-5">
                <div class="video-wrap">
                    {!! $page->video !!}
                </div>
            </div>
        </div>
        
        <section>
            <h2 class="h2">ВИАН Дизель в цифрах</h2>
            <div class="grid-x grid-padding-x">
                <div class="cell small-12 medium-12 large-3">
                    <span class="indicator">10</span>
                    <p class="title-indicator">Лет на рынке</p>
                </div>
                <div class="cell small-12 medium-12 large-3">
                    <span class="indicator">8500</span>
                    <p class="title-indicator">Клиентов</p>
                </div>
                <div class="cell small-12 medium-12 large-3">
                    <span class="indicator">21 000</span>
                    <p class="title-indicator">Отремонтированных<br>форсунок</p>
                </div>
                <div class="cell small-12 medium-12 large-3">
                    <span class="indicator">8</span>
                    <p class="title-indicator">Специалистов</p>
                </div>
            </div>
        </section>

        {{-- Производители --}}
        @include('project.composers.vendors.section')

        {{-- Сотрудники --}}
        @include('project.composers.staff.section')

        {{-- Клиенты --}}
        @include('project.composers.clients.section')

    </main>
</div>
@push('scripts')
@endpush