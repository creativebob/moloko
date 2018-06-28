{{-- Количество чего-либо --}}
{{ Form::text($name, $value, ['class'=>'varchar-field name-field compact', 'maxlength'=>'40', 'autocomplete'=>'off', 'pattern'=>'[0-9\W\s]{0,10}', $required]) }}
<div class="sprite-input-right find-status" id="name-check"></div>
<span class="form-error">Введите количество</span>
