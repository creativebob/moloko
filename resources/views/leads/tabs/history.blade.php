@isset($leadHistory)
<div class="wrap-autofind">
    <legend>Найдены обращения: {{ $leadHistory->count() }}</legend>



        <div class="grid-x">
            <div class="small-12 medium-12 large-12 cell">
                <table class="">
                    @foreach($leadHistory as $historyLead)
                        <tr>
                            <td>
                                {{ $historyLead->created_at->format('d.m.Y') }}
                            </td>
                            <td>
                                <span class="lead-name" id="lead-name"
                                      title="Использовать данные">{{ $historyLead->client->clientable->name ?? $historyLead->name}}</span><br>
                                <span id="lead-city"
                                      data-city-id="{{$historyLead->location->city->id ?? ''}}">{{$historyLead->location->city->name ?? '- Город не указан -'}}</span>,
                                <span id="lead-address">{{ $historyLead->location->address ?? ''}}</span>
                            </td>
                            <td>
                                {{ $historyLead->choice->name ?? '' }}<br>
                                {{ $historyLead->stage->name }}
                            </td>
                            <td>
                                @if(!empty($historyLead->manager->first_name))
                                    {{ $historyLead->manager->first_name . ' ' . $historyLead->manager->second_name }}
                                @else
                                    Не назначен
                                @endif
                                <br>
                                №: <a href="/admin/leads/{{ $historyLead->id }}/edit"
                                      id="{{ $historyLead->id }}"
                                      title="Перейти">{{ $historyLead->case_number ?? ''}}</a>
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


</div>
@endisset
