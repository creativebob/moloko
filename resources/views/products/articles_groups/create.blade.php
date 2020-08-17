@extends('layouts.app')

@section('title', 'Новая группа артикулов')

@section('breadcrumbs', Breadcrumbs::render('create', $pageInfo))

@section('title-content')
<div class="top-bar head-content">
    <div class="top-bar-left">
        <h2 class="header-content">ДОБАВЛЕНИЕ НОВОЙ ГРУППЫ артикулов</h2>
    </div>
    <div class="top-bar-right">
    </div>
</div>
@endsection

@section('content')

{{ Form::open(['route' => 'articles_groups.store', 'data-abide', 'novalidate']) }}
@include('products.articles_groups.form', ['submit_text' => 'Добавить'])
{{ Form::close() }}

@endsection

@section('scripts')
@include('includes.scripts.inputs-mask')

<script type="application/javascript">
	// При смене категории единиц измерения меняем список единиц измерения
    $(document).on('change', '#select-units_categories', function() {
        $.post('/admin/get_units_list', {units_category_id: $(this).val()}, function(html){
            $('#select-units').html(html);
        });
    });
</script>

@endsection



