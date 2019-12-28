<template>

    <ul id="drilldown" class="vertical menu" data-drilldown data-back-button='<li class="js-drilldown-back"><a tabindex="0">Назад</a></li>'>
        <childrens-component
            v-for="catalogGoodsItem in actualCatalogsGoodsItems"
            :item="catalogGoodsItem"
            :key="catalogGoodsItem.id"
            @get="getPrices"
        ></childrens-component>
    </ul>

</template>

<script>
    export default {
        components: {
            'childrens-component': require('../catalogs/common/CatalogsItemsChildrensComponent.vue')
        },
        name: 'drilldown',
        props: {
            actualCatalogsGoodsItems: Array,
        },
        mounted() {
            this.drilldown = new Foundation.Drilldown($('#drilldown'), {
                // These options can be declarative using the data attributes
                animationDuration: 500,
            });
        },
        data() {
            return {
            };
        },
        methods: {
            getPrices(id) {
                this.$emit('get', id);
            },
            reInit() {
                let timerId = setTimeout(function() {
                    this.drilldown = Foundation.reInit($('#drilldown'));
                }, 1);
            }
        },
        destroyed() {
            this.drilldown.destroy();
        },
    };
</script>
