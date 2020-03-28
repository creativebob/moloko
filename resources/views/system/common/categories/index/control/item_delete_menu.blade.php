@can('delete', $item)
@if(empty($item->childrens))
<div class="icon-list-delete sprite" data-open="item-delete-ajax"></div>
@endif
@endcan