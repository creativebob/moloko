<div class="grid-x">
    <div class="cell small-12 large-5">
        <div class="grid-x grid-padding-x">
            <div class="small-12 large-6 cell">
                <label>Алиас
                    @include('includes.inputs.alias', ['value'=>$company->alias, 'name'=>'alias'])
                </label>
            </div>

            <div class="cell small-12 large-4">
                <label>Сумма для начисления поинтов
                    <digit-component
                        name="points_rate"
                        @isset($company->points_rate)
                        :value="{{ $company->points_rate }}"
                        @endisset
                    ></digit-component>
                </label>
            </div>

            @if ($company->external_control == 0)
                <div class="small-12 medium-6 cell">
                    <label>Система налогообложения
                        @include('includes.selects.taxation_types', ['placeholder' => 'Не указана'])
                    </label>
                </div>
                <div class="small-12 cell">
                    <fieldset>
                        <legend>Валюты</legend>
                        @include('includes.lists.currencies')
                    </fieldset>
                </div>
            @endif

            {{ Form::hidden('external_control', 0) }}
            <div class="small-12 cell checkbox">
                {{ Form::checkbox('external_control', 1, null, ['id' => 'external_control']) }}
                <label for="external_control"><span>Внешний контроль</span></label>
            </div>

            {{-- Предлагаем добавить компанию в производители, если, конечно, создаем ее не из под страницы создания производителей --}}
            @empty($manufacturer)
                @empty($company->manufacturer)
                    {!! Form::hidden('is_manufacturer', 0) !!}
                    @can('index', App\Manufacturer::class)
                        <div class="small-12 cell checkbox">
                            {{ Form::checkbox('is_manufacturer', 1, isset($company->manufacturer), ['id' => 'checkbox-is_manufacturer']) }}
                            <label for="checkbox-is_manufacturer"><span>Производитель</span></label>
                        </div>
                    @endcan
                @endempty
            @endempty
            {{--            @empty($manufacturer)--}}
            {{--                @can('index', App\Manufacturer::class)--}}
            {{--                    @if(isset($company->manufacturer_self))--}}
            {{--                        @if($company->manufacturer_self == false)--}}
            {{--                            <div class="small-12 cell checkbox">--}}
            {{--                                {{ Form::checkbox('manufacturer_self', 1, $company->manufacturer_self, ['id' => 'manufacturer_self']) }}--}}
            {{--                                <label for="manufacturer_self"><span>Производитель</span></label>--}}
            {{--                            </div>--}}
            {{--                        @endif--}}
            {{--                    @endif--}}
            {{--                @endcan--}}
            {{--            @endempty--}}


            {{-- Предлагаем добавить компанию в поставщики, если, конечно, создаем ее не из под страницы создания поставщиков --}}
            @empty($supplier)
                @empty($company->supplier)
                {!! Form::hidden('is_supplier', 0) !!}
                @can('index', App\Supplier::class)
                    <div class="small-12 cell checkbox">
                        {{ Form::checkbox('is_supplier', 1, isset($company->supplier), ['id' => 'checkbox-is_supplier']) }}
                        <label for="checkbox-is_supplier"><span>Поставщик</span></label>
                    </div>
                @endcan
            @endempty
            @endempty
            {{--            @can('index', App\Supplier::class)--}}
            {{--                @if(empty($supplier))--}}
            {{--                    @if(isset($company->supplier_self) && (Auth::user()->company_id != null))--}}
            {{--                        @if($company->supplier_self == false)--}}
            {{--                            <div class="small-12 cell checkbox">--}}
            {{--                                {{ Form::checkbox('supplier_self', 1, $company->supplier_self, ['id' => 'supplier_self']) }}--}}
            {{--                                <label for="supplier_self"><span>Поставщик</span></label>--}}
            {{--                            </div>--}}
            {{--                        @endif--}}

            {{--                    @endif--}}
            {{--                @endif--}}
            {{--            @endcan--}}

            {{-- Предлагаем добавить компанию в клиенты, если, конечно, создаем ее не из под страницы создания клиентов --}}
            @empty($client)
                @empty($company->client)
                {!! Form::hidden('is_client', 0) !!}
                @can('index', App\Client::class)
                    <div class="cell small-12 checkbox">
                        {{ Form::checkbox('is_client', 1, isset($company->client), ['id' => 'checkbox-is_client']) }}
                        <label for="checkbox-is_client"><span>Клиент</span></label>
                    </div>
                @endcan
            @endempty
            @endempty


            @empty($agent)
                @empty($company->agent)
                    @if($company->id != auth()->user()->company_id)
                    {!! Form::hidden('is_agent', 0) !!}
                    @can('index', App\Agent::class)
                        <div class="cell small-12 checkbox">
                            {{ Form::checkbox('is_agent', 1, isset($company->agent), ['id' => 'checkbox-is_agent']) }}
                            <label for="checkbox-is_agent"><span>Агент</span></label>
                        </div>
                    @endcan
                        @endif
                @endempty
            @endempty

            {{-- Чекбоксы управления --}}
            @include('includes.control.checkboxes', ['item' => $company])
        </div>
    </div>
</div>
