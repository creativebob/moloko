<div class="grid-x grid-padding-x">

    <div class="small-12 medium-6 cell">
        {{--                        <prices-goods-discount-component--}}
        {{--                            :item="{{ $priceService }}"--}}
        {{--                        ></prices-goods-discount-component>--}}
        <div class="grid-x grid-padding-x">

            <div class="cell small-12 medium-4">
                <label>Цена
                    <digit-component
                        name="price"
                        :value="{{ $priceService->price }}"
                        :required="true"
                    ></digit-component>
                    {{--                                    {!! Form::number('percent', null, ['disabled' => $disabled]) !!}--}}
                </label>
            </div>

            <div class="cell small-12 medium-4">
                <label>Внут. валюта
                    <digit-component
                        name="points"
                        :value="{{ $priceService->points }}"
                        :decimal-place="0"
                    ></digit-component>
                    {{--                                    {!! Form::number('points', $priceService->points, ['required']) !!}--}}
                </label>
            </div>



            {{--                            <div class="cell small-12 medium-4">--}}
            {{--                                <label>Тип скидки--}}
            {{--                                    {!! Form::select('discount_mode', [1 => 'Проценты', 2 => 'Валюта'], $priceService->discount_mode, ['required']) !!}--}}
            {{--                                </label>--}}
            {{--                            </div>--}}

            <div class="small-12 cell checkbox">
                {!! Form::hidden('is_show_price', 0) !!}
                {!! Form::checkbox('is_show_price', 1, $priceService->is_show_price, ['id' => 'checkbox-is_show_price']) !!}
                <label for="checkbox-is_show_price"><span>Показывать старую цену</span></label>
            </div>

            <div class="small-12 cell checkbox">
                {!! Form::hidden('status', 0) !!}
                {!! Form::checkbox('status', 1, $priceService->status, ['id' => 'checkbox-status']) !!}
                <label for="checkbox-status"><span>Продан</span></label>
            </div>

            <div class="small-12 cell checkbox">
                {!! Form::hidden('is_hit', 0) !!}
                {!! Form::checkbox('is_hit', 1, $priceService->is_hit, ['id' => 'checkbox-is_hit']) !!}
                <label for="checkbox-is_hit"><span>Хит</span></label>
            </div>

            <div class="small-12 cell checkbox">
                {!! Form::hidden('is_new', 0) !!}
                {!! Form::checkbox('is_new', 1, $priceService->is_new, ['id' => 'checkbox-is_new']) !!}
                <label for="checkbox-is_new"><span>Новинка</span></label>
            </div>

            <div class="small-12 cell checkbox">
                {!! Form::hidden('is_priority', 0) !!}
                {!! Form::checkbox('is_priority', 1, $priceService->is_priority, ['id' => 'checkbox-is_priority']) !!}
                <label for="checkbox-is_priority"><span>Приоритет продажи для менеджеров</span></label>
            </div>

        </div>

    </div>

    @include('includes.control.checkboxes', ['item' => $priceService])

    {{-- Кнопка --}}
    <div class="small-12 cell tabs-button tabs-margin-top">
        {{ Form::submit('Редактировать', ['class' => 'button']) }}
    </div>
</div>
