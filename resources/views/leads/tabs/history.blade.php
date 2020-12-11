<lead-history-component
    @isset($leadHistory)
    :lead-history='@json($leadHistory)'
    @endisset
></lead-history-component>

{{--<div class="grid-x wrap-autofind">--}}
{{--    <div class="cell small-12">--}}
{{--        <legend>Найдены обращения: {{ $leadHistory->count() }}</legend>--}}

{{--        <table class="hover">--}}
{{--            @foreach($leadHistory as $historyLead)--}}
{{--                <tr>--}}
{{--                    <td>--}}
{{--                        {{ $historyLead->created_at->format('d.m.Y') }}--}}
{{--                    </td>--}}
{{--                    <td>--}}
{{--                                <span class="lead-name" id="lead-name"--}}
{{--                                      title="Использовать данные">{{ $historyLead->client->clientable->name ?? $historyLead->name}}</span><br>--}}
{{--                        <span id="lead-city"--}}
{{--                              data-city-id="{{$historyLead->location->city->id ?? ''}}">{{$historyLead->location->city->name ?? '- Город не указан -'}}</span>,--}}
{{--                        <span id="lead-address">{{ $historyLead->location->address ?? ''}}</span>--}}
{{--                    </td>--}}
{{--                    <td>--}}
{{--                        {{ $historyLead->choice->name ?? '' }}<br>--}}
{{--                        {{ $historyLead->stage->name }}--}}
{{--                    </td>--}}
{{--                    <td>--}}
{{--                        @if(!empty($historyLead->manager->first_name))--}}
{{--                            {{ $historyLead->manager->first_name . ' ' . $historyLead->manager->second_name }}--}}
{{--                        @else--}}
{{--                            Не назначен--}}
{{--                        @endif--}}
{{--                        <br>--}}
{{--                        №: <a href="/admin/leads/{{ $historyLead->id }}/edit"--}}
{{--                              id="{{ $historyLead->id }}"--}}
{{--                              title="Перейти">{{ $historyLead->case_number ?? ''}}</a>--}}
{{--                    </td>--}}
{{--                </tr>--}}
{{--            @endforeach--}}
{{--        </table>--}}
{{--    </div>--}}
{{--</div>--}}
