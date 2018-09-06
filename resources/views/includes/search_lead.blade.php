<div class="wrap-autofind-leads">
	@if(!empty($result_search))
            <legend>Найдены обращения:</legend>
            <div class="grid-x">
                <div class="small-12 medium-12 large-12 cell">
                    <table class="">
                    @foreach($result_search as $lead)
                        <tr>
                            <td>
                                {{ $lead->created_at->format('d.m.Y') }}
                            </td>
                            <td>
                                <span class="lead-name" id="lead-name" title="Использовать данные">
                                	<a href="/admin/leads/{{ $lead->id }}/edit" id="{{ $lead->id }}" title="Перейти">
                                		{{ $lead->name or ''}}
                                	</a>
                                </span><br>
                                <span id="lead-city" data-city-id="{{ $lead->location->city->id or ''}}">{{ $lead->location->city->name or '- Город не указан - '}}</span>,
                                <span id="lead-address">{{ $lead->location->address or '' }}</span>
                            </td>
                            <td>
                                {{ decorPhone($lead->phone) }}
                            </td>
                            <td>
                                {{ $lead->choices_goods_categories->implode('name', ',') }}
					            {{ $lead->choices_services_categories->implode('name', ',') }}
					            {{ $lead->choices_raws_categories->implode('name', ',') }}
                            </td>
                            <td>
                                {{ $lead->stage->name or ''}}<br>
                   				{{ num_format($lead->badget, 0) }} руб.
                            </td>
                            <td>
                                @if(!empty($lead->manager->first_name))
                                	{{ $lead->manager->first_name or '' }}
                                @else
                                Не назначен
                                @endif
                                <br>
                                №: <a href="/admin/leads/{{ $lead->id }}/edit" id="{{ $lead->id }}" title="Перейти">{{ $lead->case_number or '' }}</a>
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