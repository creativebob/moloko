@if(empty(auth()->user()->subscriber) || !auth()->user() || Cookie::get('request') == null)
{!! Form::open(['route' => 'project.forms.subscribe', 'data-abide', 'novalidate']) !!}
    <div class="grid-x grid-padding-x align-center wrap-form-call">
        <div class="cell small-12 text-center">
            <h2>{{ $title ?? 'Подпишитесь на нашу рассылк' }}</h2>
            <label>
                {!! Form::text('name', null, ['placeholder' => 'Имя']) !!}
            </label>
            <label>
                {!! Form::email('email', null, ['class'=>'email-field', 'maxlength'=>'30', 'autocomplete'=>'off', 'pattern'=>'^[-._a-z0-9]+@(?:[a-z0-9][-a-z0-9]+\.)+[a-z]{2,6}$', 'placeholder' => 'Email', 'required']) !!}
                <span class="form-error">Введите все символы электронной почты!</span>
            </label>
            <p>Если вы нажали на кнопку, то согласны на обработку персональных данных</p>

            {!! Form::submit('Подписаться!', ['class' => 'button']) !!}
        </div>
    </div>
    {!! Form::close() !!}

    @push('scripts')
        @include('includes.scripts.inputs-mask')
    @endpush
@endif
