<div class="grid-x grid-padding-x tabs-margin-top">

    <div class="cell small-12 medium-6 large-5">
        <div class="grid-x grid-padding-x">
            <div class="small-12 medium-12 cell">
                @include('includes.selects.loyalties', ['value' => $client->loyalty_id])
            </div>

            <div class="small-12 medium-6 cell">
                <label>Оценка работы клиентом:
                    {!! Form::select('loyalty_score', [
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                    '5' => '5',
                    '6' => '6',
                    '7' => '7',
                    '8' => '8',
                    '9' => '9',
                    '10' => '10',
                    ], optional($client->loyalty_score)->loyalty_score,
                     ['placeholder' => isset($client->loyalty_score) ? null : 'Не указано']
                     ) !!}
                </label>
            </div>

            <div class="small-12 medium-6 cell checkbox">
                {!! Form::hidden('is_blacklist', 0) !!}
                {{ Form::checkbox('is_blacklist', 1, isset($client->actual_blacklist), ['id' => 'checkbox-is_blacklist']) }}
                <label for="checkbox-is_blacklist"><span>В чёрном списке</span></label>
            </div>

            <div class="small-12 medium-6 cell checkbox">
                {!! Form::hidden('is_vip', 0) !!}
                {{ Form::checkbox('is_vip', 1, $client->is_vip, ['id' => 'checkbox-is_vip']) }}
                <label for="checkbox-is_vip"><span>VIP</span></label>
            </div>



            <div class="small-12 medium-12 cell">
                <label>Комментарий к клиенту
                    @include('includes.inputs.textarea', ['name'=>'description_client', 'value' => $client->description])
                </label>
            </div>
        </div>
    </div>

    <div class="cell small-12 medium-5 medium-offset-1 large-5 large-offset-2">
        <div class="grid-x grid-padding-x">

            <div class="small-12 medium-6 cell">
                <label>Скидка (%)
                    {!! Form::number('discount', $client->discount) !!}
{{--                    @include('includes.inputs.count', ['name' => 'discount', 'value' => $client->discount])--}}
                </label>
            </div>

            <div class="small-12 medium-6 cell">

                <label>Поинты
                    {!! Form::number('points', $client->points) !!}
{{--                    @include('includes.inputs.count', ['name' => 'points', 'value' => $client->points])--}}
                </label>
            </div>

            <div class="small-12 cell">
                <fieldset>
                    <legend>Показатели клиента</legend>
                    <table>
                        <tbody>
                        <tr>
                            <td>Статус</td>
                            <td>{{ $client->is_lost == 1 ? "Ушедший" : "Действующий" }}</td>
                        </tr>
                            <tr>
                                <td>Дата первого заказа</td>
                                <td>{{ $client->first_order_date->format('d.m.Y') }}</td>
                            </tr>
                            <tr>
                                <td>Дата последнего заказа</td>
                                <td>{{ $client->last_order_date->format('d.m.Y') }}</td>
                            </tr>
                            <tr>
                                <td>Срок жизни</td>
                                <td>{{ $client->lifetime }}</td>
                            </tr>
                            <tr>
                                <td>Кол-во заказов</td>
                                <td>{{ $client->orders_count }}</td>
                            </tr>
                            <tr>
                                <td>Частота заказов</td>
                                <td>{{ $client->purchase_frequency }}</td>
                            </tr>
                            <tr>
                                <td>Среднее время между покупками</td>
                                <td>{{ $client->ait }}</td>
                            </tr>
                            <tr>
                                <td>Клиентский капитал</td>
                                <td>{{ num_format($client->customer_equity, 0) }}</td>
                            </tr>
                            <tr>
                                <td>Средний чек</td>
                                <td>{{ num_format($client->average_order_value, 0) }}</td>
                            </tr>
                            <tr>
                                <td>Ценность клиента</td>
                                <td>{{ num_format($client->customer_value, 0) }}</td>
                            </tr>
                            <tr>
                                <td>Пожизненная ценность</td>
                                <td>{{ num_format($client->ltv, 0) }}</td>
                            </tr>
                            <tr>
                                <td>RFM-анализ</td>
                                <td>{{ $client->rfm }}</td>
                            </tr>
                            <tr>
                                <td>ABC-анализ</td>
                                <td>{{ $client->abc }}</td>
                            </tr>
                            <tr>
                                <td>Динамика активности</td>
                                <td>{{ $client->activity }}</td>
                            </tr>
                        </tbody>
                    </table>
                </fieldset>
            </div>


        </div>
    </div>

</div>
