@if($catalogs_services_data['catalogsServices']->isNotEmpty())
    <catalog-services-component
        :catalogs-services-data='@json($catalogs_services_data)'
    ></catalog-services-component>
@endif
