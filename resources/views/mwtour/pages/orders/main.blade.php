<div class="grid-x grid-padding-x">
    <main class="cell small-12 main-content">

        {{-- Заголовок --}}
        @include('mwtour.pages.common.title')

		@include('project.composers.navigations.navigation_by_align', ['align' => 'left'])
        <div class="grid-x">
            <div class="cell small-12">
                <ul class="my-tours-list">

                    {{-- <li>
                        <div class="grid-x">
                            <div class="small-12 medium-5 wrap-my-tour-img cell">
                                <img src="/img/mwtour/services/1.jpg" class="service_photo" alt="" title="">
                            </div>
                            <div class="small-12 cell medium-7 wrap-my-tour-info">
                                <span>Бронь №</span><span>000388</span>
                                <h2>Байкальский трип</h2>
                                <span>Стартуем </span><span>15 июля 2021</span>
                            </div>
                        </div>
                    </li> --}}

                </ul>
            </div>
            <div class="cell small-12">

            </div>

        </div>
    </main>
</div>
