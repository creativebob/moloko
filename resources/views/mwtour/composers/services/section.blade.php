@if ($services->isNotEmpty())
    <ul class="grid-x small-up-1 medium-up-1 large-up-2 list-tours">
        @foreach($services as $service)

        <li class="cell small-12">
            <div class="wrap-item">
                <h2>{{ $service->process->name }}</h2>

                <span class="data-date">{{ $service->actualFlows->first()->start_at->translatedFormat('j F') }} - {{ $service->actualFlows->first()->finish_at->translatedFormat('j F') }}</span>

                <div class="wrap-service-photo">
                    <img src="{{ getPhotoPathPlugEntity($service) }}"
                        alt="{{ $service->process->name }}"
                        title=""
                        @if(isset($service->process->photo))
                        width="530"
                        height="246"
                        @endif
                        class="service_photo"
                    >
                    <div class="wrap-service-duration">
                        <span class="count-day-tour">{{ $service->actualFlows->first()->start_at->diffInDays($service->actualFlows->first()->finish_at) + 1 }}</span>
                        <span class="desc-day-tour">дней</span>
                    </div>

                </div>

                <div class="wrap-participant">
                    <span>Участников: </span>
                    <span class="count-participants">{{ $service->actualFlows->first()->clients->count() }}</span> из <span>{{ $service->actualFlows->first()->capacity_max }}</span>
                </div>

                <p class="service-content">{!! $service->process->description !!}</p>
                <div class="wrap-button">
                    <a href="{{ route('project.tours.show', $service->process->slug) }}" title="" class="button blue">Ознакомиться</a>
                </div>
            </div>
        </li>

        @endforeach
    </ul>
@endif

