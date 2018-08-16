{{-- Выводим только категории --}}

@foreach($grouped_items as $category)

@if (empty($id))
@php
$id = null;
@endphp
@endif

@include('includes.menu-views.category-list', ['grouped_items' => $grouped_items, 'categories' => $category, 'class' => $class, 'entity' => $entity, 'type' => $type, 'id' => $id])

@endforeach
