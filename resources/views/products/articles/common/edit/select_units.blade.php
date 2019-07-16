
{{-- Ловим иное имя поля если такое отправили --}}
@php if(!isset($field_name)){$field_name = 'unit_id';}; @endphp

{{-- Ловим источник данных для поля --}}
@php if(!isset($data)){$data = $article->group->unit_id;}; @endphp

{!! Form::select($field_name, $units->pluck('name', 'id'), $data, ['id' => 'select-units', isset($disabled) ? 'disabled' : '']) !!}
