<div class="grid-x grid-padding-x">
    <div class="cell small-12 medium-6">
        @if($shift->exists && empty($shift->closed_at))
            {!! Form::model($shift, ['route' => ['shift.close', $shift->id], 'data-abide', 'novalidate']) !!}
            @method('PATCH')
        @else
            {!! Form::open(['route' => 'shift.open', 'data-abide', 'novalidate']) !!}
        @endif

        <fieldset>
            <legend>Инфо</legend>

            <div class="grid-x grid-padding-x">

                @if($shift->exists)
                <div class="cell small-12">
                    <table>
                        <tbody>
                            <tr>
                                <td>Дата</td>
                                <td>{{ $shift->date->format('d.m.Y') }}</td>
                            </tr>
                            <tr>
                                <td>Торговая точка</td>
                                <td>{{ $shift->outlet->name }}</td>
                            </tr>
                            <tr>
                                <td>Баланс открытия</td>
                                <td>{{ num_format($shift->balance_open, 0) }}</td>
                            </tr>
                            <tr>
                                <td>Наличка</td>
                                <td>{{ num_format($shift->cash, 0) }}</td>
                            </tr>
                            <tr>
                                <td>Баланс закрытия</td>
                                <td>{{ num_format($shift->balance_close, 0) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                @endif

                @if(!$shift->exists)
                        @if(count(session('access.user_info.outlets')) > 1)
                            <div class="cell small-12 medium-6">
                                <label>Торговая точка
                                    <select name="outlet_id">
                                        @foreach(session('access.user_info.outlets') as $outlet)
                                            <option
                                                @isset(request()->outlet_id)
                                                @if(request()->outlet_id == $outlet->id)
                                                selected
                                                @endif
                                                @endisset
                                                value="{{ $outlet->id }}"
                                            >{{ $outlet->name }}</option>
                                        @endforeach
                                    </select>
                                </label>
                            </div>
                        @else
                            {!! Form::hidden('outlet_id', session('access.user_info.outlets')[0]->id) !!}
                        @endif

                    <div class="cell small-4 align-center">
                        {{ Form::submit('Открыть', ['class'=>'button']) }}
                    </div>
                @endif

                @if($shift->exists && empty($shift->closed_at))
                    <div class="cell small-4 align-center">
                        {{ Form::submit('Закрыть', ['class'=>'button']) }}
                    </div>
                @endif
            </div>
        </fieldset>
        {!! Form::close() !!}
    </div>

    <div class="cell small-12 medium-6">
        {!! Form::open(['route' => 'shift', 'method' => 'GET', 'data-abide', 'novalidate']) !!}
        <fieldset>
            <legend>Фильтр</legend>
            <div class="grid-x grid-padding-x">
                <div class="cell small-12 medium-6">
                    <label>Дата
                        <pickmeup-component
                            :required="true"
                            @isset(request()->date)
                            value="{{ \Carbon\Carbon::createFromFormat('d.m.Y', request()->date) }}"
                            @endisset
                        ></pickmeup-component>
                    </label>
                </div>
                @if(count(session('access.user_info.outlets')) > 1)
                    <div class="cell small-12 medium-6">
                        <label>Торговая точка
                            <select name="outlet_id">
                                @foreach(session('access.user_info.outlets') as $outlet)
                                    <option
                                        @isset(request()->outlet_id)
                                        @if(request()->outlet_id == $outlet->id)
                                        selected
                                        @endif
                                        @endisset
                                        value="{{ $outlet->id }}"
                                    >{{ $outlet->name }}</option>
                                @endforeach
                            </select>
                        </label>
                    </div>
                @else
                    {!! Form::hidden('outlet_id', session('access.user_info.outlets')[0]->id) !!}
                @endif

                <div class="cell small-4 align-center">
                    {!! Form::submit('Выбрать', ['class'=>'button']) !!}
                </div>

                <div class="cell small-4 align-center">
                    <a href="{{ route('shift') }}" class="button">Сбросить</a>
                </div>
            </div>
        </fieldset>
        {!! Form::close() !!}
    </div>

</div>
