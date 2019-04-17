{{-- Выносим чтобы ajax мог выделять чекбоксы --}}
{{ Form::model($category, []) }}
@include('includes.category_metrics.properties_list')
{{ Form::close() }}