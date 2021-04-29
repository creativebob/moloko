@if($pricesServicesWithImpacts->isNotEmpty())
    <div class="grid-x align-center">
        <div class="small-12 cell">
            <ul class="grid-x grid-margin-x small-up-2 medium-up-3 large-up-4 impacts-list">

                @php
                    $count = 1;
                @endphp

                @foreach($pricesServicesWithImpacts as $priceService)
                    @foreach($priceService->service->process->impacts as $impact)
                        <li class="cell text-center">
                            @if(isset($impact->article->manufacturer->company->color) || isset($impact->article->manufacturer->company->photo))
                            <div>
                                <img
                                    src="{{ isset($impact->article->manufacturer->company->color) ? $impact->article->manufacturer->company->color->path : $impact->article->manufacturer->company->photo->path }}"
                                    alt="Логотип {{ $impact->article->manufacturer->company->name }}" width="90" height="90">
                            </div>

                            @else
                                @if(empty($impact->article->manufacturer_id))
                                    <div>
                                        Любой
                                    </div>
                                    @endif
                            @endisset
                            @isset($impact->article->photo)
                            <div>
                                <img src="{{ $impact->article->photo->path }}"  alt="{{ $impact->article->name }}" width="440" height="292">
                            </div>
                            @endif
                            <div>
                                    <span class="price-service-impact">{{ num_format($priceService->price, 0) }}<span
                                            class="currency">{{ $priceService->currency->abbreviation }}</span></span>
                            </div>
                        </li>

                        @php
                            $count++;
                        @endphp

                        @if($count > 4)
                            @break
                        @endif
                    @endforeach
                    @if($count > 4)
                        @break
                    @endif
                @endforeach
            </ul>
        </div>
    </div>
@endif
