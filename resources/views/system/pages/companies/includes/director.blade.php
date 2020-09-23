<director-component
    @isset($director)
        :director="{{ $director }}"
    @endisset
    :cities='@json($cities)'
    :city='@json($city)'
    @if($company->id != auth()->user()->company_id)
        :access-block="true"
        @endif
></director-component>
{{--<div class="cell small-12 large-6">--}}

{{--    <fieldset>--}}
{{--        <legend>Директор (руководитель)</legend>--}}
{{--        <div class="grid-x grid-padding-x">--}}
{{--            <div class="small-12 cell">--}}
{{--                <label>Фамилия--}}
{{--                    @include('includes.inputs.name', ['name' => 'second_name', 'value' => $director->second_name ?? null, 'required' => true])--}}
{{--                </label>--}}
{{--            </div>--}}
{{--            <div class="small-12 cell">--}}
{{--                <label>Имя--}}
{{--                    @include('includes.inputs.name', ['name' => 'first_name', 'value'=>$director->first_name ?? null, 'required' => true])--}}
{{--                </label>--}}
{{--            </div>--}}
{{--            <div class="small-12 cell">--}}
{{--                <label>Отчество--}}
{{--                    @include('includes.inputs.name', ['name '=> 'patronymic', 'value'=>$director->patronymic ?? null])--}}
{{--                </label>--}}
{{--            </div>--}}
{{--            <div class="small-12 medium-6 cell">--}}
{{--                <label>Телефон--}}
{{--                    @include('includes.inputs.phone', ['value' => optional($director->main_phone)->phone ?? null, 'name'=>'user_phone', 'required' => true, 'id' => 'main-phone'])--}}
{{--                </label>--}}
{{--            </div>--}}

{{--            <input type="hidden" name="user_country_id" value="1">--}}

{{--            <div class="small-12 medium-6 cell">--}}
{{--                @include('system.common.includes.city_search', ['item' => $director ?? auth()->user(), 'required' => true, 'name' => 'user_city_id'])--}}
{{--                                    @php isset(Auth::user()->location->city->name) ? $city_default = Auth::user()->location->city : $city_default = null; @endphp--}}
{{--                                    @include('includes.inputs.city_search', ['city' => isset($director->location->city->name) ? $director->location->city : $city_default, 'id' => 'cityForm2', 'required' => true, 'field_name' => 'user_city_id'])--}}
{{--            </div>--}}

{{--            <div class="small-12 medium-12 cell">--}}
{{--                <label>Адрес--}}
{{--                    @include('includes.inputs.address', ['value' => optional($director->location)->address, 'name' => 'user_address'])--}}
{{--                </label>--}}
{{--            </div>--}}

{{--        </div>--}}
{{--    </fieldset>--}}
{{--</div>--}}
