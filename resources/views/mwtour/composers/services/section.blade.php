@if ($services->isNotEmpty())
    <ul class="grid-x small-up-1 medium-up-1 large-up-2 list-tours">
        @foreach($services as $service)

        <li class="cell small-12">
            <div class="wrap-item">
                <h2>{{ $service->process->name }}</h2>

                <button type="button" data-toggle="dropdown-{{$service->id}}" class="drop-date-button">
                        <span class="data-date">
                            <span class="first-date">{{ $service->actualFlows->first()->start_at->translatedFormat('j F') }} - {{ $service->actualFlows->first()->finish_at->translatedFormat('j F') }}</span>
                            <span class="change-date">Выберите дату тура:</span>
                            <span class="arrow-dropdown"></span>
                        </span>
                </button>
                <ul class="dropdown-pane list-date" data-close-on-click="true" data-position="bottom" data-alignment="left" id="dropdown-{{$service->id}}" data-dropdown data-auto-focus="true">
                    <li>
                        <a href="{{ route('project.tours.show', [$service->process->slug, 'flow_id' => $service->actualFlows->first()->id]) }}">{{ $service->actualFlows->first()->start_at->translatedFormat('j F') }} - {{ $service->actualFlows->first()->finish_at->translatedFormat('j F') }}</a>
                    </li>
                    <li>
                        <a href="{{ route('project.tours.show', [$service->process->slug, 'flow_id' => $service->actualFlows->first()->id]) }}">{{ $service->actualFlows->first()->start_at->translatedFormat('j F') }} - {{ $service->actualFlows->first()->finish_at->translatedFormat('j F') }}</a>
                    </li>
                </ul>
                <div class="wrap-service-photo">
                    <a href="{{ route('project.tours.show', [$service->process->slug, 'flow_id' => $service->actualFlows->first()->id]) }}" title="">
                        <img src="{{ getPhotoPathPlugEntity($service) }}"
                            alt="{{ $service->process->name }}"
                            title=""
                            @if(isset($service->process->photo))
                            width="530"
                            height="246"
                            @endif
                            class="service_photo"
                        >
                    </a>
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
                    <a href="{{ route('project.tours.show', [$service->process->slug, 'flow_id' => $service->actualFlows->first()->id]) }}" title="" class="button">Ознакомиться</a>
                </div>
            </div>
        </li>

        @endforeach
    </ul>
@endif

