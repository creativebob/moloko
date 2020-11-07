<template>
    <div class="grid-x grid-padding-x payment-block">

        <store-component
            v-if="showStoreComponent"
            :currencies="currencies"
        ></store-component>

        <div
            class="cell small-12"
            v-if="payments.length"
        >
            <table class="unstriped">
                <thead>
                <tr>
                    <th>Дата</th>
                    <th>Тип</th>
                    <th>Сумма</th>
                    <th></th>
                </tr>
                </thead>

                <tbody>
                <payment-component
                    v-for="(payment, index) in payments"
                    :payment="payment"
                    :index="index"
                    :key="payment.id"
                    @remove="removePayment"
                ></payment-component>
                </tbody>

                <tfoot>
                <tr>
                    <td colspan="2" class="text-right">Итого</td>
                    <td>{{ paymentsAmount | decimalPlaces | decimalLevel }}</td>
                    <td></td>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
</template>

<script>
    export default {
        components: {
            'store-component': require('./StoreComponent'),
            'payment-component': require('./PaymentComponent'),
        },

        props: {
            currencies: {
                type: Array,
                default() {
                    return [
                        {
                            id: 1,
                            name: 'Рубль',
                        },
                    ]
                }
            },
        },

        computed: {
            showStoreComponent() {
                return this.$store.getters.PAYMENTS_TOTAL < this.$store.getters.ESTIMATE_AGGREGATIONS.estimate.total;
            },
            payments() {
                return this.$store.state.lead.payments;
            },
            paymentsAmount() {
                return this.$store.getters.PAYMENTS_TOTAL;
            },
        },
        methods: {
            removePayment(index) {
                this.$store.dispatch('REMOVE_PAYMENT', index);
            }
        },
        filters: {
            decimalPlaces(value) {
                return parseFloat(value).toFixed(2);
            },
            decimalLevel: function (value) {
                return parseFloat(value).toLocaleString();
            },
        }
    }
</script>
