<template>
    <div class="cell small-12">
        <div class="grid-x grid-padding-x">
            <div class="cell shrink self-left">
                <digit-component
                    classes="input-payment electronically"
                    :value="estimateTotal"
                    :disabled="true"
                ></digit-component>
            </div>
            <div class="cell shrink self-right">
                <button
                    @click="resetType"
                    type="button"
                    class="button tiny button-payment-back"
                >Назад
                </button>
            </div>
        </div>

        <div class="grid-x grid-padding-x">
            <div class="cell small-12 invert-show">
                <button
                    @click="addPayment"
                    type="button"
                    class="button"
                >Принять оплату и закрыть чек</button>
            </div>
        </div>
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
        methods: {
            resetType() {
                this.$emit('reset');
            },
            addPayment() {
                const data = {
                    cash: 0,
                    electronically: this.estimateTotal,
                };
                this.$store.dispatch('ADD_PAYMENT', data);
            }
        }
    }
</script>
