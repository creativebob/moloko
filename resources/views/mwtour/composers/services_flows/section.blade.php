@if ($servicesFlows->isNotEmpty())
    <ul class="grid-x small-up-1 medium-up-1 large-up-2 list-tours">
        @foreach($servicesFlows as $serviceFlow)

        <li class="cell small-12">
            <div class="wrap-item">
                <h2>{{ $serviceFlow->process->process->name }}</h2>
                <span class="data-date">{{ $serviceFlow->start_at->translatedFormat('j F') }} - {{ $serviceFlow->finish_at->translatedFormat('j F') }}</span>
                <div class="wrap-service-photo">
                    <a href="{{ route('project.tours.show', $serviceFlow->process->process->slug) }}" title="">
                        <img src="{{ getPhotoPathPlugEntity($serviceFlow->process) }}"
                            alt="{{ $serviceFlow->process->process->name }}"
                            title=""
                            @if(isset($serviceFlow->process->process->photo))
                            width="530"
                            height="246"
                            @endif
                            class="service_photo"
                        >
                    </a>
                    <div class="wrap-service-duration">
                        <span class="count-day-tour">{{ $serviceFlow->start_at->diffInDays($serviceFlow->finish_at) + 1 }}</span>
                        <span class="desc-day-tour">дней</span>
                    </div>
                </div>
                <div class="wrap-participant">
                    <span>Участников: </span>
                    <span class="count-participants">{{ $serviceFlow->clients->count() }}</span> из <span>{{ $serviceFlow->capacity_max }}</span>
                </div>
                <p class="service-content">{!! $serviceFlow->process->process->description !!}</p>
                <div class="wrap-button">
                    <a href="{{ route('project.tours.show', $serviceFlow->process->process->slug) }}" title="" class="button blue contur">Ознакомиться</a>
                </div>
            </div>
        </li>

        @endforeach
    </ul>
@endif

