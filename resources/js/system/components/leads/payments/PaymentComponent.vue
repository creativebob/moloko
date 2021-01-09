<template>
    <tr
        :class="[{ 'canceled' : canceled}]"
    >
        <td>{{ payment.registered_at | formatDate }}</td>
        <td>{{ type }}</td>
        <td>{{ payment.method.name }}</td>
        <td>{{ payment.total | decimalPlaces | decimalLevel }} {{ payment.currency.abbreviation }}</td>
        <td class="td-delete">
            <template
                v-if="userHasOutlet"
            >
                <div
                    v-if="canRemove"
                    class="icon-delete sprite"
                    @click="removePayment"
                ></div>
                <div
                    v-if="canCancel"
                    @click="openModalCancel"
                    class="icon-delete sprite"
                    data-open="modal-payment-cancel"
                ></div>
            </template>
        </td>
    </tr>
</template>

<script>
import moment from 'moment'

export default {
    props: {
        payment: Object,
        index: Number,
    },
    computed: {
        type() {
            if (this.payment.cash > 0 && this.payment.electronically == 0) {
                return 'Наличный';
            }

            if (this.payment.cash == 0 && this.payment.electronically > 0) {
                return 'Безналичный';
            }

            if (this.payment.cash > 0 && this.payment.electronically > 0) {
                return 'Смешанный';
            }
        },
        canRemove() {
            return !this.$store.getters.HAS_OUTLET_SETTING('use-cash-register') && this.$store.getters.HAS_OUTLET_SETTING('payment-edit') && this.$store.state.lead.estimate.conducted_at === null && this.payment.canceled_at == null;
        },
        canCancel() {
            return this.$store.getters.HAS_OUTLET_SETTING('use-cash-register') && this.$store.getters.HAS_OUTLET_SETTING('payment-edit') && this.$store.state.lead.estimate.conducted_at === null && this.payment.canceled_at == null;
        },
        canceled() {
            return this.payment.canceled_at !== null;
        },
        userHasOutlet() {
            return this.$store.getters.USER_HAS_OUTLET;
        }
    },
    methods: {
        openModalCancel() {
            const data = {
                id: this.payment.id,
                total: this.payment.total
            };
            this.$emit('open-modal-cancel', data);
        },
        removePayment() {
            this.$store.dispatch('REMOVE_PAYMENT', this.payment.id);
        }
    },
    filters: {
        decimalPlaces(value) {
            return parseFloat(value).toFixed(2);
        },
        decimalLevel: function (value) {
            return parseFloat(value).toLocaleString();
        },

        formatDate: function (value) {
            if (value) {
                return moment(String(value)).format('DD.MM.YYYY')
            }
        },
    }
}
</script>
