<div class="grid-x grid-padding-x">

    <main class="cell small-12 main-content">
        <div class="grid-x grid-padding-x">
            <div class="cell small-12 medium-12 large-8 wrap-main-content">
               <h1>{{ $page->seo->h1 ?? $page->name }}</h1>
               <p>{!! $page->content !!}</p>
            </div>
            <div class="cell small-12 medium-12 large-4">

            </div>
        </div>

        {{-- Туры --}}
        @include('project.composers.services_flows.section')

    </main>
</div>

@push('scripts')
@endpush
