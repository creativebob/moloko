{{-- Выводим только категории --}}

@foreach($grouped_items as $category)

@if (empty($id))
@php
$id = null;
@endphp
@endif

@include('catalogs.category-list', ['grouped_items' => $grouped_items, 'categories' => $category, 'class' => $class, 'entity' => $entity, 'type' => $type, 'id' => $id])

@endforeach
