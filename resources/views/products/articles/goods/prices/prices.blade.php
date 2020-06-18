<prices-goods-component
    :catalogs-data='@json($catalogsData)'
    :cur-goods="{{ $item }}"
></prices-goods-component>

@push('scripts')
    {{-- Скрипт отображения на сайте --}}
    @include('includes.scripts.ajax-display')
@endpush
