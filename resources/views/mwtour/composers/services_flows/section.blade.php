@if ($servicesFlows->isNotEmpty())
    <ul class="grid-x grid-margin-x small-up-1 medium-up-1 large-up-2 list-tours">
        @foreach($servicesFlows as $serviceFlow)
        <li class="cell small-12">
            <h2>{{ $serviceFlow->process->process->name }}</h2>
            <span>{{ $serviceFlow->start_at->format('d F') }} - {{ $serviceFlow->finish_at->format('d F') }}</span>
            <div class="wrap-service-photo">
                <img src="{{ getPhotoPathPlugEntity($serviceFlow->process) }}"
                     alt="{{ $serviceFlow->process->process->name }}"
                     title=""
                     @if(isset($serviceFlow->process->process->photo))
                     width="440"
                     height="292"
                    @endif
                     class="service_photo"
                >
                <div class="wrap-service-duration">
                    <span>{{ $serviceFlow->start_at->diffInDays($serviceFlow->finish_at) }}</span>
                    <div>дней</div>
                </div>
            </div>
            <div class="wrap-participant">
                <span>Участников: </span>
                <span>{{ $serviceFlow->clients->count() }}</span> из <span>{{ $serviceFlow->capacity_max }}</span>
            </div>
            <p class="service-content">{!! $serviceFlow->process->process->content !!}</p>
            <a href="{{ route('project.tours.show', $serviceFlow->process->process->slug) }}" title="" class="button blue">Ознакмиться</a>
        </li>
        @endforeach
    </ul>
@endif

