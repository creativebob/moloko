<template>
    <div class="grid-x grid-padding-x">
        <div class="cell small-12 large-9  cmv-indicators">
            <div class="grid-x grid-margin-x">
                <div class="cell shrink">
                    <span class="indicator-name">Вес: </span><span class="indicators_total">{{ totalWeight }}</span> <span>гр.</span>
                </div>
                <div class="cell auto">
                    <span class="indicator-name">Себестоимость: </span><span class="indicators_total">{{ totalCost }}</span> <span>руб.</span>
                </div>
            </div>
        </div>
    </div>
<!--    <div class="grid-x grid-padding-x">-->
<!--        <fieldset class="small-12 cell goods-indicators">-->
<!--            <legend> Расчет по товару:</legend>-->
<!--            <div class="grid-x grid-padding-x">-->
<!--                <div class="small-12 cell indicators-item">-->
<!--                    <span>Общая себестоимость: </span><span class="indicators_total">{{ totalCost }}</span><span> руб.</span>-->
<!--                </div>-->
<!--                <div class="small-12 cell indicators-item">-->
<!--                    <span>Общий вес: </span><span class="indicators_total">{{ totalWeight }}</span><span> гр.</span>-->
<!--                </div>-->
<!--            </div>-->
<!--        </fieldset>-->
<!--    </div>-->
</template>

<script>
    export default {
        computed: {
            totalWeight() {
                return this.$store.getters.totalWeight;
            },
            totalCost() {
                return this.$store.getters.totalCost;
            },
        },
    }
</script>
