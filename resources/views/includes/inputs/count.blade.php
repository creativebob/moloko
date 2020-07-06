{{-- Количество чего-либо --}}
{{ Form::text($name, ($value ?? null), ['class' => 'varchar-field name-field compact', 'maxlength' => '40', 'autocomplete' => 'off', 'pattern' => '[0-9\W\s]{0,10}', (isset($required) ? 'required' : '')]) }}
<div class="sprite-input-right find-status" id="name-check"></div>
<span class="form-error">Введите количество</span>
