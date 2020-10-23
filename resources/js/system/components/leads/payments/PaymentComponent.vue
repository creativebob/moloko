<template>
    <tr>
        <td>{{ payment.registered_at | formatDate }}</td>
        <td>{{ type }}</td>
        <td>{{ payment.total | decimalPlaces | decimalLevel }} {{ payment.currency.abbreviation }}</td>
    </tr>
</template>

<script>
    import moment from 'moment'

    export default {
        props: {
            payment: Object
        },
        computed: {
            type() {
                if (this.payment.cash > 0 && this.payment.electronically == 0) {
                    return 'Наличный';
                }

                if (this.payment.cash == 0 && this.payment.electronically > 0) {
                    return 'Терминал';
                }
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
                    return moment(String(value)).format('DD.MM.YYYY HH:mm')
                }
            },
        }
    }
</script>
