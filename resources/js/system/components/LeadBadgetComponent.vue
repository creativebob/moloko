<template>

<!--        <input-digit-component-->
<!--                name="badget"-->
<!--                rate="2"-->
<!--                :value="estimateTotal"-->

<!--        ></input-digit-component>-->
        <input
                name="badget"
                type="number"
                :value="estimateTotal"

                >


</template>

<script>
    export default {
            components: {
                    'input-digit-component': require('./InputDigitComponent.vue')
            },
        computed: {
                estimateTotal() {
                        return Number(this.$store.getters.estimateTotal);
                },
        },
    }
</script>
