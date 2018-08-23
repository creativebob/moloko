{{-- Форма звонка --}}
{{ Form::open(['data-abide', 'novalidate', 'class' => 'form-request']) }}
<h5>Закажите звонок:</h5>
<label>
{{ Form::text('name', null, ['class'=>'name-field', 'maxlength'=>'30', 'autocomplete'=>'off', 'pattern'=>'[A-Za-zА-Яа-яЁё/-/s]{3,30}', 'placeholder'=>'ВАШЕ ИМЯ']) }}
</label>
<label>
{{ Form::text('name', null, ['class'=>'phone-field', 'maxlength'=>'17', 'autocomplete'=>'off', 'pattern'=>'[0-9/(/)/-/s]{17}', 'placeholder'=>'НОМЕР ТЕЛЕФОНА']) }}
</label>
{{ Form::submit('Позвоните мне!', ['class'=>'button']) }}
{{ Form::close() }}