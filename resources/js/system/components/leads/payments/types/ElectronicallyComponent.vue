<template>
    <div class="cell shrink">
        <label>Сумма:
            <digit-component
                classes="input-payment electronically"
                :value="estimateTotal"
                :disabled="true"
            ></digit-component>
        </label>
    </div>
</template>

<script>
export default {
    components: {
        'digit-component': require('../../../inputs/DigitComponent'),
    },
    computed: {
        estimateTotal() {
            return this.$store.getters.ESTIMATE_AGGREGATIONS.estimate.total;
        }
    },
}
</script>
