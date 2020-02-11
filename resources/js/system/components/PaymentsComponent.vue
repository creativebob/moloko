<template>
    <div class="grid-x grid-padding-x">
        <div
            v-if="!isSaled"
            class="cell small-12"
        >
            <div class="grid-x grid-padding-x">

                <div class="cell small-3">
                    <label>Сумма:
                        <input
                            type="number"
                            v-model="amount"
                        >
                    </label>
                </div>

                <div class="cell small-3">
                    <label>Тип платежа:
                        <select
                            v-model="paymentsTypeId"
                        >
                            <option
                                v-for="paymentsType in paymentsTypes"
                                :value="paymentsType.id"
                            >{{ paymentsType.name }}</option>
                        </select>
                    </label>
                </div>

                <div class="cell small-3">
                    <label>Дата:
                        <input
                            type="text"
                            class="date-field"
                            autocomplete="off"
                            pattern="[0-9]{2}.[0-9]{2}.[0-9]{4}"
                            required
                            v-model="date"
                        >
                        <span class="form-error">Выберите дату!</span>
                    </label>
                </div>

                <div class="cell small-3">
                    <button
                        class="button"
                        @click.prevent="addPayment"
                    >Добавить</button>
                </div>

            </div>
        </div>

        <div
            class="cell small-12"
            v-if="payments.length"
        >
            <table>
                <thead>
                    <tr>
                        <th>Дата</th>
                        <th>Тип</th>
                        <th>Сумма</th>
                    </tr>
                </thead>

                <tbody>
                    <tr
                        v-for="payment in payments"
                    >
                        <td>{{ payment.date | formatDate}}</td>
                        <td>{{ payment.type.name }}</td>
                        <td>{{ payment.amount | roundToTwo | level }}</td>
                    </tr>
                </tbody>

                <tfoot>
                    <tr>
                        <td colspan="2" class="text-right">Итого</td>
                        <td>{{ paymentsAmount | roundToTwo | level }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

</template>

<script>
    import moment from 'moment'

	export default {
		props: {
			document: Object,
            paymentsTypes: Array,
            curDate: String
		},

        data() {
            return {
                saled: this.document.is_saled,
                amount: 0,
                paymentsTypeId: this.paymentsTypes[0].id,
                date: moment(String(this.curDate)).format('DD.MM.YYYY'),
                payments: this.document.payments
            }
        },

        computed: {
		    isSaled() {
		        return this.saled == 1 || this.$store.getters.paymentsAmount >= this.$store.getters.estimateTotal;
            },
            paymentsAmount() {
                return this.$store.getters.paymentsAmount;
            }
        },
        methods: {
            addPayment() {

                let data = {
                    amount: this.amount,
                    payments_type_id: this.paymentsTypeId,
                    date: this.date,

                    contract_id: this.document.lead.client.contract.id,
                    contract_type: 'App\\ContractsClient',

                    document_id: this.document.id,
                    document_type: 'App\\Estimate'
                };
                // console.log(data);

                axios
                    .post('/admin/payments', data)
                    .then(response => {
                        let payment = response.data;
                        this.payments.push(payment);
                        this.$store.commit('ADD_PAYMENT', payment);
                        this.amount = 0;
                    })
                    .catch(error => {
                        console.log(error)
                    });

            }
        },

        filters: {
            roundToTwo: function (value) {
                return Math.trunc(parseFloat(Number(value).toFixed(2)) * 100) / 100;
            },

            // Создает разделители разрядов в строке с числами
            level: function (value) {
                return Number(value).toLocaleString();
            },

            formatDate: function (value) {
                if (value) {
                    return moment(String(value)).format('DD.MM.YYYY')
                }
            },
        },
	}
</script>
