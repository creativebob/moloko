@if ($servicesFlows->isNotEmpty())
    <ul class="grid-x grid-margin-x small-up-1 medium-up-1 large-up-2 list-tours">
        @foreach($servicesFlows as $serviceFlow)
        <li class="cell small-12">
            <h2>{{ $serviceFlow->process->process->name }}</h2>
            <span>{{ $serviceFlow->start_at->format('d') }} {{ $serviceFlow->start_at->format('F') }}  -  {{ $serviceFlow->finish_at->format('d') }} {{ $serviceFlow->finish_at->format('F') }}</span>
            <div class="wrap-service-photo">
                <img src="/img/mwtour/services/1.jpg" class="service_photo" alt="" title="">
                <div class="wrap-service-duration">
                    <span>{{ $serviceFlow->start_at->diff($serviceFlow->finish_at)->days }}</span>
                    <div>дней</div>
                </div>
            </div>
            <div class="wrap-participant">
                <span>Участников: </span>
                <span>{{ $serviceFlow->capacity_min }}</span> из <span>{{ $serviceFlow->capacity_max }}</span>
            </div>
            <p class="service-content">{!! $serviceFlow->process->process->content !!}</p>
            <a href="{{ route('project.tours.show', $serviceFlow->process->process->slug) }}" title="" class="button blue">Ознакмиться</a>
        </li>

        @endforeach
            <li class="cell small-12">
                <h2>Байкальское путешествие</h2>
                <span>14 ИЮНЯ  -  28 ИЮНЯ</span>
                <div class="wrap-service-photo">
                    <img src="/img/mwtour/services/2.jpg" class="service_photo" alt="" title="">
                    <div class="wrap-service-duration">
                        <span>12</span>
                        <div>дней</div>
                    </div>
                </div>
                <div class="wrap-participant">
                    <span>Участников: </span>
                    <span>9</span> из <span>20</span>
                </div>
                <p class="service-content">От Иркутска до лагеря 260 километров в сторону деревни Сарма (Ольхонский район). Бухта "Хужир-Нугэ" является археологическим памятником - здесь найдены стоянки древних людей-курыкан.</p>
                <a href="/tour" title="" class="button blue">Ознакмиться</a>
            </li>
    </ul>
@endif

