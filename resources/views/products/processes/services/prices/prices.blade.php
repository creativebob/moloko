<prices-service-component
    :catalogs-data='@json($catalogsData)'
    :service="{{ $item }}"
></prices-service-component>

@push('scripts')
    {{-- Скрипт отображения на сайте --}}
    @include('includes.scripts.ajax-display')
@endpush
