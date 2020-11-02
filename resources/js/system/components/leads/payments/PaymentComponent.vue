<template>
    <tr>
        <td>{{ payment.registered_at | formatDate }}</td>
        <td>{{ type }}</td>
        <td>{{ payment.total | decimalPlaces | decimalLevel }} {{ payment.currency.abbreviation }}</td>
        <td class="td-delete">
            <div
                @click="removePayment"
                class="icon-delete sprite"
            ></div>
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
            }
        },
        methods: {
            removePayment() {
                this.$emit('remove', this.index);
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
