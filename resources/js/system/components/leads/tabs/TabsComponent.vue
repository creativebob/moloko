<template>
    <ul class="tabs-list" data-tabs id="tabs-leads">
        <li class="tabs-title is-active">
            <a href="#tab-events" aria-selected="true">События</a>
        </li>

        <catalog-goods-component></catalog-goods-component>
        <catalog-services-component></catalog-services-component>
        <payment-component></payment-component>

        <li class="tabs-title">
            <a data-tabs-target="tab-attribution" href="#tab-attribution">Аттрибуция</a>
        </li>
    </ul>
</template>

<script>
export default {
    components: {
        'payment-component': require('./PaymentsComponent'),
        'catalog-goods-component': require('./CatalogGoodsComponent'),
        'catalog-services-component': require('./CatalogServicesComponent'),
    },
}
</script>
