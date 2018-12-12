{{-- Выносим чтобы ajax мог выделять чекбоксы --}}
{{ Form::model($category, []) }}
@include('includes.metrics_category.properties_list')
{{ Form::close() }}