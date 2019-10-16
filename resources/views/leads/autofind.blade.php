
    <div class="wrap-autofind">

        @if(!empty($finded_leads))
            <legend>Найдены обращения:</legend>
            <div class="grid-x">
                <div class="small-12 medium-12 large-12 cell">
                    <table class="">
                    @foreach($finded_leads as $lead)
                        <tr>
                            <td>
                                {{ $lead->created_at->format('d.m.Y') }}
                            </td>
                            <td>
                                <span class="lead-name" id="lead-name" title="Использовать данные">{{$lead->name ?? ''}}</span><br>
                                <span id="lead-city" data-city-id="{{$lead->location->city->id ?? ''}}">{{$lead->location->city->name ?? '- Город не указан -'}}</span>,
                                <span id="lead-address">{{ $lead->location->address ?? ''}}</span>
                            </td>
                            <td>
                                {{ $lead->choice->name ?? '' }}<br>
                                {{ $lead->stage->name }}
                            </td>
                            <td>
                                @if(!empty($lead->manager->first_name))
                                {{ $lead->manager->first_name . ' ' . $lead->manager->second_name }}
                                @else
                                Не назначен
                                @endif
                                <br>
                                №: <a href="/admin/leads/{{ $lead->id }}/edit" id="{{ $lead->id }}" title="Перейти">{{ $lead->case_number ?? ''}}</a>
                            </td>
                        </tr>
                    @endforeach
                    </table>
                </div> 
            </div>

            {{--
            <legend>Заказы:</legend>
            <div class="grid-x">
                <div class="small-12 medium-12 large-12 cell">
                    <table>

                    </table>
                </div> 
            </div>
            --}}
        @endif

    </div>