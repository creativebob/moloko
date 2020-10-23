<template>
    <div class="cell small-12">
        <div class="grid-x grid-padding-x">
            <div class="cell shrink self-left">
                Банк
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
    </div>
</template>

<script>
    export default {
        methods: {
            resetType() {
                this.$emit('reset');
            },
        }
    }
</script>
