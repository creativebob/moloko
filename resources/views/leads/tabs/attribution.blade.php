<div class="grid-x grid-padding-x">
    <div class="cell small-12">
        <table class="table-attributions">
            <tr>
            <tr>
                <td>Тип обращения:</td>
                <td id="lead-type-name">{{ $lead->lead_type->name ?? ''}}</td>
                <td>

                    @if (($lead->manager_id == Auth::user()->id) || (Auth::user()->staff[0]->position_id == 4))
                        <a id="change-lead-type" class="button tiny">Изменить</a>
                    @endif

                </td>
            </tr>
            <tr>
                <td>Способ обращения:</td>
                <td>

                    {{-- Будем мутить селект в ручную --}}

                    {{-- @php
                    if($lead->lead_method->mode != 1){

                    $disabled_method_list = 'disabled';} else {
                    $disabled_method_list = '';};
                    @endphp --}}

                    @include('includes.selects.lead_methods', ['lead_method_id' => $lead->lead_method_id])

                </td>
                <td></td>
            </tr>
            <td>Интерес:</td>
            <td>
                {{ Form::select('choice_tag', $choices, genChoiceTag($lead), ['disabled' => ($lead->lead_method_id == 2)]) }}</td>
            <td></td>
            </tr>
            <tr>
                <td>Предварительная стоимость:</td>
                <td>
                    <digit-component
                        name="badget"
                        :value="{{ $lead->badget }}"

                        @isset($lead->site_id)
                        :disabled="true"
                        @endisset
                    ></digit-component>
                </td>
                <td></td>
            </tr>
            <tr>
                <td>Склад списания:</td>
                <td>
                    @php
                        $disabled = null;
                            if ($lead->estimate->saled_at || $lead->estimate->is_reserved == 1) {
                            $disabled = true;
                        }
                    @endphp
                    {{--                                                @if ($stocks->isNotEmpty())--}}
                    {{--                                                <select-stocks-component :stock-id="{{ $lead->estimate->stock_id }}" :stocks='@json($stocks)'></select-stocks-component>--}}
                    {{--                                                @include('includes.selects.stocks', ['stock_id' => $lead->estimate->stock_id, 'disabled' =>  $disabled])--}}
                    {{--                                                    @endif--}}
                </td>
                <td></td>
            </tr>
            <tr>
                <td>Источник:</td>
                <td>{{ $lead->source->name ?? ''}}</td>
                <td></td>
            </tr>
            <tr>
                <td>Сайт:</td>
                <td>{{ $lead->site->name ?? ''}}</td>
                <td></td>
            </tr>
            <tr>
                <td>Тип трафика:</td>
                <td>{{ $lead->medium->name ?? ''}}</td>
                <td></td>
            </tr>
            <tr>
                <td>Рекламная кампания:</td>
                <td>{{ $lead->campaign_id ?? ''}}</td>
                <td></td>
            </tr>
            <tr>
                <td>Объявление:</td>
                <td>{{ $lead->utm_content ?? ''}}</td>
                <td></td>
            </tr>
            <tr>
                <td>Ключевая фраза:</td>
                <td>{{ $lead->utm_term ?? ''}}</td>
                <td></td>
            </tr>
            <tr>
                <td>Менеджер:</td>
                <td>{{ $lead->manager->name }}</td>
                <td>
                    @if (extra_right('lead-appointment') || (extra_right('lead-appointment-self') && ($lead->manager_id == Auth::user()->id)) || ($lead->manager_id == Auth::user()->id))
                        <a id="lead-free" class="button tiny">Освободить</a>
                    @endif
                </td>
            </tr>
            <tr>
                <td>Активных задач:</td>
                <td>{{ $lead->challenges_active_count ?? ''}}</td>
                <td></td>
            </tr>
        </table>


    </div>

    <div class="cell small-12">
        <button-unregister-component></button-unregister-component>
    </div>
</div>
