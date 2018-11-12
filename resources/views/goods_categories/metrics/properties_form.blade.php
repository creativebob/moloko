{{ Form::model($goods_category, []) }}
@include('goods_categories.metrics.properties_list', ['properties' => $properties, 'set_status' => $set_status])
{{ Form::close() }}