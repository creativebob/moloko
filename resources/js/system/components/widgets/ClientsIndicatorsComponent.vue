<template>
    <div>
        <div>
            <template v-if="yearsList.length > 1">
                <select
                    v-model="year"
                    @change="getIndicatorsForYear"
                    :disabled="disabledSelect"
                >
                    <option
                        v-for="year in yearsList"
                        :value="year"
                    >{{ year }}</option>
                </select>
            </template>
        </div>
        <table class="widget-table stack unstriped hover responsive-card-table table-clients_indicators">
            <thead>
                <tr>
                    <th class="right-border">Показатель</th>
                    <template v-for="month in months">
                        <th
                            @click="getIndicatorsForMonth(month.number)"
                            :class="[{disabled : isDisabled(month.number)}, {current : curMonth == month.number && curYear == year}]"
                            class="loading"
                        >{{ month.name }}
                            <div v-if="month.loading" class="sprite-input-right icon-load"></div>
                        </th>
                    </template>
                </tr>
            </thead>

            <tbody>
                <tr v-for="item in data">
                    <td class="right-border">{{ item.name }}</td>
                    <template v-for="month in months">
                        <td v-if="curIndicators[month.number]">{{ curIndicators[month.number][item.alias] }}</td>
                        <td v-else>0</td>
                    </template>
                </tr>
            </tbody>

        </table>
    </div>
</template>

<script>
    export default {
        props: {
            curYear: String,
            curMonth: Number,
            yearsList: Array,
            clientsIndicators: Object
        },
        data() {
            return {
                months: [
                    {
                        number: 1,
                        name: 'Январь',
                        loading: false
                    },
                    {
                        number: 2,
                        name: 'Февраль',
                        loading: false
                    },
                    {
                        number: 3,
                        name: 'Март',
                        loading: false
                    },
                    {
                        number: 4,
                        name: 'Апрель',
                        loading: false
                    },
                    {
                        number: 5,
                        name: 'Май',
                        loading: false
                    },
                    {
                        number: 6,
                        name: 'Июнь',
                        loading: false
                    },
                    {
                        number: 7,
                        name: 'Июль',
                        loading: false
                    },
                    {
                        number: 8,
                        name: 'Август',
                        loading: false
                    },
                    {
                        number: 9,
                        name: 'Сентябрь',
                        loading: false
                    },
                    {
                        number: 10,
                        name: 'Октябрь',
                        loading: false
                    },
                    {
                        number: 11,
                        name: 'Ноябрь',
                        loading: false
                    },
                    {
                        number: 12,
                        name: 'Декабрь',
                        loading: false
                    },
                ],
                data: [
                    {
                        name: 'Общее количество контактов',
                        alias: 'count'
                    },
                    {
                        name: 'Количество "Действующих" клиентов на начало периода',
                        alias: 'active_previous_count'
                    },
                    {
                        name: 'Количество "Действующих" клиентов',
                        alias: 'active_count'
                    },
                    {
                        name: 'Количество "Потерянных" клиентов',
                        alias: 'lost_count'
                    },
                    {
                        name: 'Количество исключеных из базы',
                        alias: 'deleted_count'
                    },
                    {
                        name: 'Количество контактов в черном списке',
                        alias: 'blacklist_count'
                    },
                    {
                        name: 'Кол-во новых клиентов в периоде',
                        alias: 'new_clients_period_count'
                    },
                    {
                        name: 'Кол-во ушедших клиентов в периоде',
                        alias: 'lost_clients_period_count'
                    },
                    {
                        name: 'Коэффициент удержания',
                        alias: 'customer_retention_rate'
                    },
                    {
                        name: 'Оформленные сметы',
                        alias: 'orders_count'
                    },
                    {
                        name: 'Оформленные сметы за период',
                        alias: 'orders_period_count'
                    },
                    {
                        name: 'Коэффициент оттока',
                        alias: 'churn_rate'
                    },
                    {
                        name: 'Количество покупателей из числа клиентов в периоде',
                        alias: 'customers_period_count'
                    },
                    {
                        name: 'Коэффициент закрытия лидов',
                        alias: 'lead_close_rate'
                    },
                    {
                        name: 'Коэффициент удовлетворенности (Коэффициент повторных покупок)',
                        alias: 'repeat_purchase_rate'
                    },

                    {
                        name: 'Частота заказов',
                        alias: 'purchase_frequency'
                    },

                    {
                        name: 'Частота заказов за период',
                        alias: 'purchase_frequency_period'
                    },
                    {
                        name: 'Средний промежуток времени между покупками',
                        alias: 'order_gap_analysis'
                    },
                    {
                        name: 'Общая выручка',
                        alias: 'orders_revenue'
                    },
                    {
                        name: 'Общая выручка за период',
                        alias: 'orders_revenue_period'
                    },
                    {
                        name: 'Средний доход от клиента за период',
                        alias: 'arpu'
                    },
                    {
                        name: 'Средний доход от платящего клиента за период',
                        alias: 'arppu'
                    },
                    {
                        name: 'Доля платящих клиентов',
                        alias: 'paying_share'
                    },
                    {
                        name: 'Срок жизни по методике',
                        alias: 'lifetime'
                    },
                    {
                        name: 'Срок жизни по нашим расчетам',
                        alias: 'lifetime_fact'
                    },
                    {
                        name: 'Средний чек',
                        alias: 'average_order_value'
                    },
                    {
                        name: 'Средний чек за период',
                        alias: 'average_order_value_period'
                    },
                    {
                        name: 'Ценность клиента',
                        alias: 'customer_value'
                    },
                    {
                        name: 'Ценность клиента за период',
                        alias: 'customer_value_period'
                    },
                    {
                        name: 'Пожизненная ценность',
                        alias: 'ltv'
                    },
                    {
                        name: 'Пожизненная ценность на основе данных за период',
                        alias: 'ltv_period'
                    },
                    {
                        name: 'Клиентский капитал',
                        alias: 'customer_equity'
                    },
                    {
                        name: 'Индекс лояльности',
                        alias: 'nps'
                    },


                ],
                year: this.curYear,
                indicators: this.clientsIndicators,
                loading: false,
                disabledSelect: false
            }
        },
        computed: {
            curIndicators() {
                return this.indicators[this.year];
            }
        },
        methods: {
            isDisabled(month) {
                if (this.curYear == this.year) {
                    return this.curMonth < month;
                } else {
                    return false;
                }
            },
            getIndicatorsForYear() {
                if(! this.indicators[this.year]) {
                    Vue.set(this.indicators, this.year, {});

                    // axios
                    //     .post('/admin/clients_indicators/year', {
                    //         year: this.year
                    //     })
                    //     .then(response => {
                    //
                    //     })
                    //     .catch(error => {
                    //         console.log(error)
                    //     });
                }
            },
            getIndicatorsForMonth(number) {
                if (! this.isDisabled(number)) {
                    var year = this.year;
                    var month = this.months.find(obj => obj.number == number);
                    console.log(month);
                    month.loading = true;
                    this.disabledSelect = true;
                    axios
                        .post('/admin/clients_indicators/compute', {
                            month: number,
                            year: year
                        })
                        .then(response => {
                            Vue.set(this.indicators[year], number, response.data);
                            month.loading = false;
                            this.disabledSelect = false;
                        })
                        .catch(error => {
                            console.log(error);
                            month.loading = false;
                            this.disabledSelect = false;
                        });
                }
            },
        }
    }
</script>
