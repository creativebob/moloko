<div class="wrap-autofind-leads">
	@if(!empty($result_search))
            <div class="grid-x">
                <div class="small-12 medium-12 large-12 cell wrap-table-autofind-leads">
                    <table class="">
                    @foreach($result_search as $lead)
                        <tr>
                            <td>
                                {{ $lead->created_at->format('d.m.Y') }}
                            </td>
                            <td>
                                <span class="lead-name" id="lead-name" title="Использовать данные">
                                	<a href="/admin/leads/{{ $lead->id }}/edit" id="{{ $lead->id }}" title="Перейти">
                                		{{ $lead->name ?? ''}}
                                	</a>
                                </span><br>
                                <span id="lead-city" data-city-id="{{ $lead->location->city->id ?? ''}}">{{ $lead->location->city->name ?? '- Город не указан - '}}</span>,
                                <span id="lead-address">{{ $lead->location->address ?? '' }}</span>
                            </td>
                            <td>
                                {{ decorPhone($lead->main_phone->phone) }}
                            </td>
                            <td>
                                {{ $lead->choice->name ?? '' }}

                            </td>
                            <td>
                                {{ $lead->stage->name ?? ''}}<br>
                   				{{ num_format($lead->badget, 0) }} руб.
                            </td>
                            <td>
                                @if(!empty($lead->manager->first_name))
                                	{{ $lead->manager->first_name ?? '' }}
                                @else
                                Не назначен
                                @endif
                                <br>
                                №: <a href="/admin/leads/{{ $lead->id }}/edit" id="{{ $lead->id }}" title="Перейти">{{ $lead->case_number ?? '' }}</a>
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