@extends('layouts.app')

@section('title', 'Редактировать группу сырья')

@section('breadcrumbs', Breadcrumbs::render('edit', $page_info, $raws_product->name))

@section('title-content')
<div class="top-bar head-content">
    <div class="top-bar-left">
        <h2 class="header-content">РЕДАКТИРОВАНИЕ ГРУППЫ СЫРЬЯ</h2>
    </div>
    <div class="top-bar-right">
    </div>
</div>
@endsection

@section('content')

{{ Form::model($raws_product, ['route' => ['raws_products.update', $raws_product->id], 'data-abide', 'novalidate']) }}
{{ method_field('PATCH') }}
@include('raws_products.form', ['submit_text' => 'Редактировать группу сырья'])
{{ Form::close() }}

@endsection

@section('scripts')
@include('includes.scripts.inputs-mask')

<script type="text/javascript">
	// При смене категории единиц измерения меняем список единиц измерения
    $(document).on('change', '#select-units_categories', function() {
        $.post('/admin/get_units_list', {units_category_id: $(this).val()}, function(html){
            $('#select-units').html(html);
        });
    });
</script>

@endsection