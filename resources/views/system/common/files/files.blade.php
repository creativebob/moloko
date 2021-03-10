@if($item->exists)
    @can('create', App\File::class)
        <files-component
            alias="{{ $item->getTable() }}"
            :id="{{ $item->id }}"
            :item-files='@json($item->files)'
        ></files-component>
    @endcan
@endif
