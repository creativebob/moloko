<div class="grid-x grid-padding-x">

    <main class="cell small-12 main-content">

        {{-- Заголовок --}}
        @include('mwtour.pages.common.title')

        {{-- @include('project.composers.promotions.slider') --}}

        {{-- Сотрудники --}}
        {{-- @include('project.composers.staff.section') --}}


        <div class="grid-x grid-padding-x">
            <div class="cell small-12 medium-12 large-8 wrap-main-content">
               <h1>Организация активного отдыха по России</h1>
               <p>Мы делаем яркие и бодрящие туры в Восточной Сибири. Пеший туризм - наше все. Позвоните мне и я организую вам путешествие. Сочное, как давленный качан капусты.</p>
               <p>Живи, твори, мечтай!</p>
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
