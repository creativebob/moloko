@extends('layouts.app')

@section('title', 'Редактировать группу артикулов')

@section('breadcrumbs', Breadcrumbs::render('edit', $pageInfo, $articles_group->name))

@section('title-content')
<div class="top-bar head-content">
    <div class="top-bar-left">
        <h2 class="header-content">РЕДАКТИРОВАНИЕ ГРУППЫ артикулов</h2>
    </div>
    <div class="top-bar-right">
    </div>
</div>
@endsection

@section('content')

{{ Form::model($articles_group, ['route' => ['articles_groups.update', $articles_group->id], 'data-abide', 'novalidate']) }}
{{ method_field('PATCH') }}
@include('products.articles_groups.form', ['submit_text' => 'Редактировать'])
{{ Form::close() }}

@endsection

@push('scripts')
@include('includes.scripts.inputs-mask')

<script type="application/javascript">
	// При смене категории единиц измерения меняем список единиц измерения
    $(document).on('change', '#select-units_categories', function() {
        $.post('/admin/get_units_list', {units_category_id: $(this).val()}, function(html){
            $('#select-units').html(html);
        });
    });
</script>

@endpush
