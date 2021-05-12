{!! Form::open(['route' => 'project.order', 'data-abide', 'novalidate',]) !!}

<div class="grid-x align-center wrap-form-call">
    <div class="cell small-12 text-center">
        <h4>Бронировать тур</h4>
        <label>
            @auth
                {!! Form::text('first_name', auth()->user()->first_name, ['placeholder' => 'Ваше имя']) !!}
            @else
                {!! Form::text('first_name', null, ['placeholder' => 'Ваше имя']) !!}
            @endauth
        </label>
        <label>
            @auth
                {!! Form::text('main_phone', auth()->user()->main_phone->phone, ['class'=>'phone-field', 'maxlength'=>'17', 'autocomplete'=>'off', 'pattern'=>'8 \([0-9]{3}\) [0-9]{3}-[0-9]{2}-[0-9]{2}', 'placeholder' => 'Телефон', 'required', 'readonly']) !!}
            @else
                {!! Form::text('main_phone', null, ['class'=>'phone-field', 'maxlength'=>'17', 'autocomplete'=>'off', 'pattern'=>'8 \([0-9]{3}\) [0-9]{3}-[0-9]{2}-[0-9]{2}', 'placeholder' => 'Телефон', 'required']) !!}
            @endauth
            <span class="form-error">Введите все символы телефонного номера!</span>
        </label>
        {!! Form::hidden('flow_id', $flowId) !!}

        {!! Form::submit('Отправить!', ['class' => 'button']) !!}
    </div>
</div>

{!! Form::close() !!}

@push('scripts')
    @include('includes.scripts.inputs-mask')
@endpush
