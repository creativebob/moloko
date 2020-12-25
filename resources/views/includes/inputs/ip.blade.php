{{-- Инн --}}
{{ Form::text('ip', ($value ?? null), [

    'maxlength'=>'15',
    'autocomplete'=>'off',
    'pattern' => '(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)_*(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)_*){3}',
    (isset($required) ? 'required' : '')
    ]) }}
<span class="form-error">Укажите Ip</span>
{{--'class'=>'ip-field',--}}
{{--'pattern'=>'^((\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.){3}(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])$',--}}
