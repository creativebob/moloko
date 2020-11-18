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
            <table class="table-payments">
                <thead>
                    <tr>
                        <th class="item-payment-date">Дата</th>
                        <th class="item-payment-type">Тип</th>
                        <th class="item-payment-method">Способ</th>
                        <th class="item-payment-amount">Сумма</th>
                        <th class="item-payment-delete"></th>
                    </tr>
                </thead>

                <tbody>
                    <payment-component
                        v-for="(payment, index) in payments"
                        :payment="payment"
                        :index="index"
                        :key="payment.id"
                        @open-modal-cancel="openModal"
                    ></payment-component>

                </tbody>

                <tfoot>
                <tr>
                    <td colspan="3" class="text-left">Итого</td>
                    <td>{{ paymentsAmount | decimalPlaces | decimalLevel }}</td>
                    <td></td>
                </tr>
                </tfoot>
            </table>
        </div>

        <div
            v-if="canCancel"
            class="reveal rev-small"
            id="modal-payment-cancel"
            data-reveal
        >
<!--            v-reveal-->
            <div class="grid-x">
                <div class="small-12 cell modal-title">
                    <h5>Отмена</h5>
                </div>
            </div>
            <div class="grid-x align-center modal-content ">
                <div class="small-10 cell text-center">
                    <p>Отменяем платеж на сумму {{ removingPayment.total }}, вы уверены?</p>
                </div>
            </div>
            <div class="grid-x align-center grid-padding-x">
                <div class="small-6 medium-4 cell">
                    <button
                        @click.prevent="cancelPayment"
                        data-close
                        class="button modal-button"
                        type="submit"
                    >Подтвердить</button>
                </div>
                <div class="small-6 medium-4 cell">
                    <button data-close class="button modal-button" type="submit">Отменить</button>
                </div>
            </div>
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
        data() {
            return {
                removingPayment: {
                    total: 0
                },
            }
        },
        computed: {
            showStoreComponent() {
                return this.$store.getters.PAYMENTS_TOTAL < this.$store.getters.ESTIMATE_AGGREGATIONS.estimate.total;
            },
            payments() {
                return this.$store.getters.PAYMENTS;
            },
            paymentsAmount() {
                return this.$store.getters.PAYMENTS_TOTAL;
            },
            canCancel() {
                return this.$store.getters.HAS_OUTLET_SETTING('use-cash-register') && this.$store.getters.HAS_OUTLET_SETTING('payment-edit') && this.$store.state.lead.estimate.conducted_at === null;
            }
        },
        methods: {
            openModal(payment) {
                this.removingPayment = payment
            },
            cancelPayment() {
                this.$store.dispatch('CANCEL_PAYMENT', this.removingPayment.id);
                $('#modal-payment-cancel').foundation('close');
                this.removingPayment = {
                    total: 0
                };
            },
        },
        filters: {
            decimalPlaces(value) {
                return parseFloat(value).toFixed(2);
            },
            decimalLevel: function (value) {
                return parseFloat(value).toLocaleString();
            },
        },
        // directives: {
        //     'reveal': {
        //         bind: function (el) {
        //             new Foundation.Reveal($(el))
        //         },
        //     }
        // }
    }
</script>
