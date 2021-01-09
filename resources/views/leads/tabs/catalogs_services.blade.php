{{--@can('index', App\CatalogsService::class)--}}
{{--    @if($catalogs_services_data['catalogsServices']->isNotEmpty())--}}
        <catalog-services-component
            :outlet="{{ $outlet }}"
        ></catalog-services-component>
{{--    @endif--}}
{{--@endcan--}}
