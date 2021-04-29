<div class="grid-x grid-padding-x">

    <main class="cell small-12 medium-7 large-9 main-content">

        {{-- Заголовок --}}
        @include('mwtour.pages.common.title')

        {{-- @include('project.composers.promotions.slider') --}}


        {{-- Сотрудники --}}
        @include('project.composers.staff.section')

    </main>
</div>
@push('scripts')
@endpush