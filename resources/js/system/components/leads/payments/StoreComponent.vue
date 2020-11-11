<template>
    <div class="cell small-12">
        <div class="grid-x grid-padding-x selector-type-payment">
            <div
                v-if="type == null"
                class="cell small-12"
            >
                <div class="grid-x grid-padding-x selector-type-payment">
                    <div class="cell auto">
                        <button
                            v-if="showCash"
                            type="button"
                            class="button hollow button-cash-type"
                            @click="setType('cash')"
                        >Наличные
                        </button>
                        <button
                            v-if="showTerminal"
                            type="button"
                            class="button hollow button-electronically-type"
                            @click="setType('electronically')"
                        >Банковская карта
                        </button>
                    </div>
                    <div class="cell shrink">
                        <button
                            v-if="showBank"
                            type="button"
                            class="button hollow button-bank-type"
                            @click="setType('bank')"
                        >Оплата через банк
                        </button>
                    </div>
                </div>
            </div>
            <cash-component
                v-else-if="type == 'cash'"
                @reset="setType"
            ></cash-component>
            <electronically-component
                v-else-if="type == 'electronically'"
                @reset="setType"
            ></electronically-component>
            <bank-component
                v-else-if="type == 'bank'"
                @reset="setType"
            ></bank-component>
        </div>
    </div>
</template>

<script>
    export default {
        components: {
            'cash-component': require('./types/CashComponent'),
            'electronically-component': require('./types/ElectronicallyComponent'),
            'bank-component': require('./types/BankComponent'),
        },
        props: {
            currencies: Array,
        },
        data() {
            return {
                type: null,
            }
        },
        computed: {
            showCash() {
                return this.$store.getters.OUTLET_SETTING('payment-cash');
            },
            showTerminal() {
                return this.$store.getters.OUTLET_SETTING('payment-terminal');
            },
            showBank() {
                return this.$store.getters.OUTLET_SETTING('bank-account');
            },
        },
        methods: {
            setType(type = null) {
                this.type = type;
            }
        }
    }
</script>
