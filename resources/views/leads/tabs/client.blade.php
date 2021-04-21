<lead-client-component></lead-client-component>

{{--<div class="grid-x grid-padding-x">--}}
{{--    --}}{{-- <div class="small-12 medium-12 large-12 cell">--}}
{{--        <label>Страна--}}
{{--            @php--}}
{{--            $country_id = null;--}}
{{--            if (isset($lead->location->country_id)) {--}}
{{--            $country_id = $lead->location->country_id;--}}
{{--        }--}}
{{--        @endphp--}}
{{--        {{ Form::select('country_id', $countries_list, $country_id)}}--}}
{{--        </label>--}}
{{--    </div> --}}
{{--    <div class="small-12 medium-12 cell">--}}
{{--        <div class="grid-x grid-padding-x">--}}
{{--        </div>--}}
{{--        @if($lead->client)--}}
{{--            --}}{{--                                    @if ($lead->client->orders_count > 0)--}}
{{--            --}}{{--                                        <span>Клиент: <a href="{{ route('clients.edit', $lead->client_id) }}">{{ $lead->client->clientable->name ?? '' }}</a></span><br>--}}
{{--            --}}{{--                                    @endif--}}

{{--            <span>Лояльность: {{ $lead->client->loyalty->name ?? '' }}</span>--}}
{{--        @else--}}

{{--            --}}{{-- Подключаем клиентов --}}
{{--            @include('includes.contragents.fieldset', ['item' => $lead])--}}

{{--        @endif--}}
{{--    </div>--}}
{{--</div>--}}

