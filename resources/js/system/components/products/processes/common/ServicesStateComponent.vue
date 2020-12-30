<template>
    <div class="grid-x grid-padding-x">
        <div class="cell small-12 large-9  cmv-indicators">
            <div class="grid-x grid-margin-x">
                <div class="cell shrink">
                    <span class="indicator-name">Продолжительность: </span><span class="indicators_total">{{ totalLength }}</span> <span>сек.</span>
                </div>
                <div class="cell auto">
<!--                    <span class="indicator-name">Себестоимость: </span><span class="indicators_total">{{ totalCost }}</span> <span>руб.</span>-->
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        computed: {
            totalLength() {
                return this.$store.getters.SERVICES_TOTAL_LENGTH;
            },
            totalCost() {
                return this.$store.getters.SERVICES_TOTAL_COST;
            },
        },
    }
</script>
