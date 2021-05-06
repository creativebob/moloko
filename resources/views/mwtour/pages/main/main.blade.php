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



        <ul class="grid-x small-up-1 medium-up-1 large-up-2 list-tours">
            <li class="cell small-12">
                <div class="wrap-item">
                    <h2>Байкальское путешествие</h2>
                    <span class="data-date">14 ИЮНЯ  -  28 ИЮНЯ</span>
                    <div class="wrap-service-photo">
                        <img src="/img/mwtour/services/1.jpg" class="service_photo" alt="" title="">
                        <div class="wrap-service-duration">
                            <span class="count-day-tour">12</span>
                            <span class="desc-day-tour">дней</span>
                        </div>
                    </div>
                    <div class="wrap-participant">
                        <span>Участников: </span>
                        <span class="count-participants">9</span> из <span>20</span>
                    </div>
                    <p class="service-content">От Иркутска до лагеря 260 километров в сторону деревни Сарма (Ольхонский район). Бухта "Хужир-Нугэ" является археологическим памятником - здесь найдены стоянки древних людей-курыкан.</p>
                    <div class="wrap-button">
                        <a href="/tour" title="" class="button blue">Ознакмиться</a>
                    </div>
                </div>
            </li>
            <li class="cell small-12">
                <div class="wrap-item">
                    <h2>Байкальское путешествие</h2>
                    <span class="data-date">14 ИЮНЯ  -  28 ИЮНЯ</span>
                    <div class="wrap-service-photo">
                        <img src="/img/mwtour/services/2.jpg" class="service_photo" alt="" title="">
                        <div class="wrap-service-duration">
                            <span class="count-day-tour">5</span>
                            <span class="desc-day-tour">дней</span>
                        </div>
                    </div>
                    <div class="wrap-participant">
                        <span>Участников: </span>
                        <span>9</span> из <span>20</span>
                    </div>
                    <p class="service-content">От Иркутска до лагеря 260 километров в сторону деревни Сарма (Ольхонский район). Бухта "Хужир-Нугэ" является археологическим памятником - здесь найдены стоянки древних людей-курыкан.</p>
                    <div class="wrap-button">
                        <a href="/tour" title="" class="button blue">Ознакмиться</a>
                    </div>
                </div>
            </li>
        </ul>




    </main>
</div>
@push('scripts')
@endpush