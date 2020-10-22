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
                            v-if="is_cash_payment"
                            type="button"
                            class="button hollow button-cash-type"
                            @click="setType('cash')"
                        >Наличные
                        </button>
                        <button
                            v-if="is_electronically_payment"
                            type="button"
                            class="button hollow button-electronically-type"
                            @click="setType('electronically')"
                        >Банковская карта
                        </button>
                    </div>
                    <div class="cell shrink">
                        <button
                            v-if="is_bank_payment"
                            type="button"
                            class="button hollow button-bank-type"
                        >Выставить счёт
                        </button>
                    </div>
                </div>
            </div>
            <cash-component
                v-else-if="type == 'cash'"
                :is-electronically="is_electronically_payment"
                @reset="setType"
            ></cash-component>
            <electronically-component
                v-else-if="type == 'electronically'"
                @reset="setType"
            ></electronically-component>
        </div>
    </div>
</template>

<script>
    export default {
        components: {
            'cash-component': require('./types/CashComponent'),
            'electronically-component': require('./types/ElectronicallyComponent'),
        },
        props: {
            currencies: Array,
        },
        data() {
            return {
                type: null,
                is_cash_payment: true,
                is_electronically_payment: true,
                is_bank_payment: false
            }
        },
        methods: {
            setType(type = null) {
                this.type = type;
            }
        }
    }
</script>
