<template>
    <div>
        <div>
            <template v-if="yearsList.length > 1">
                <select
                    v-model="year"
                    @change="changeIndicatorsForYear"
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
                        >
                            {{ month.name }}
                            <div v-if="month.loading" class="sprite-input-right icon-load"></div>
                        </th>
                    </template>
                    <th
                        class="border-left loading"
                        @click="getIndicatorsForYear()"
                    >
                        Год
                        <div v-if="loadingYear" class="sprite-input-right icon-load"></div>
                    </th>
                </tr>
            </thead>

            <tbody>
<!--                <tr v-for="item in data">-->
<!--                    <td class="right-border">{{ item.name }}</td>-->
<!--                    <template v-for="month in months">-->
<!--                        <template v-if="curIndicatorsMonth">-->
<!--                            <td v-if="curIndicatorsMonth[month.number]">{{ curIndicatorsMonth[month.number][item.alias] }}</td>-->
<!--                            <td v-else>0</td>-->
<!--                        </template>-->
<!--                        <td v-else>0</td>-->
<!--                    </template>-->
<!--    -->
<!--                    <td v-if="curIndicatorsYear" class="border-left">{{ curIndicatorsYear[item.alias] }}</td>-->
<!--                    <td v-else class="border-left">0</td>-->
<!--                </tr>-->

                <tr>
                    <td class="right-border">Общее количество контактов</td>
                    <template v-for="month in months">
                        <template v-if="curIndicatorsMonth">
                            <td v-if="curIndicatorsMonth[month.number]">{{ curIndicatorsMonth[month.number]['count'] | level }}</td>
                            <td v-else>0</td>
                        </template>
                        <td v-else>0</td>
                    </template>

                    <td v-if="curIndicatorsYear" class="border-left">{{ curIndicatorsYear['count'] | level }}</td>
                    <td v-else class="border-left">0</td>
                </tr>
                <tr>
                    <td class="right-border">Количество "Действующих" клиентов на начало периода</td>
                    <template v-for="month in months">
                        <template v-if="curIndicatorsMonth">
                            <td v-if="curIndicatorsMonth[month.number]">{{ curIndicatorsMonth[month.number]['active_previous_count'] | level }}</td>
                            <td v-else>0</td>
                        </template>
                        <td v-else>0</td>
                    </template>

                    <td v-if="curIndicatorsYear" class="border-left">{{ curIndicatorsYear['active_previous_count'] | level }}</td>
                    <td v-else class="border-left">0</td>
                </tr>
                <tr>
                    <td class="right-border">Количество "Действующих" клиентов</td>
                    <template v-for="month in months">
                        <template v-if="curIndicatorsMonth">
                            <td v-if="curIndicatorsMonth[month.number]">{{ curIndicatorsMonth[month.number]['active_count'] | level }}</td>
                            <td v-else>0</td>
                        </template>
                        <td v-else>0</td>
                    </template>

                    <td v-if="curIndicatorsYear" class="border-left">{{ curIndicatorsYear['active_count'] | level }}</td>
                    <td v-else class="border-left">0</td>
                </tr>
                <tr>
                    <td class="right-border">Количество "Потерянных" клиентов</td>
                    <template v-for="month in months">
                        <template v-if="curIndicatorsMonth">
                            <td v-if="curIndicatorsMonth[month.number]">{{ curIndicatorsMonth[month.number]['lost_count'] | level }}</td>
                            <td v-else>0</td>
                        </template>
                        <td v-else>0</td>
                    </template>

                    <td v-if="curIndicatorsYear" class="border-left">{{ curIndicatorsYear['lost_count'] | level }}</td>
                    <td v-else class="border-left">0</td>
                </tr>
                <tr>
                    <td class="right-border">Количество исключеных из базы</td>
                    <template v-for="month in months">
                        <template v-if="curIndicatorsMonth">
                            <td v-if="curIndicatorsMonth[month.number]">{{ curIndicatorsMonth[month.number]['deleted_count'] | level }}</td>
                            <td v-else>0</td>
                        </template>
                        <td v-else>0</td>
                    </template>

                    <td v-if="curIndicatorsYear" class="border-left">{{ curIndicatorsYear['deleted_count'] | level }}</td>
                    <td v-else class="border-left">0</td>
                </tr>
                <tr>
                    <td class="right-border">Количество контактов в черном списке</td>
                    <template v-for="month in months">
                        <template v-if="curIndicatorsMonth">
                            <td v-if="curIndicatorsMonth[month.number]">{{ curIndicatorsMonth[month.number]['blacklist_count'] | level }}</td>
                            <td v-else>0</td>
                        </template>
                        <td v-else>0</td>
                    </template>

                    <td v-if="curIndicatorsYear" class="border-left">{{ curIndicatorsYear['blacklist_count'] | level }}</td>
                    <td v-else class="border-left">0</td>
                </tr>
                <tr>
                    <td class="right-border">Кол-во новых клиентов в периоде</td>
                    <template v-for="month in months">
                        <template v-if="curIndicatorsMonth">
                            <td v-if="curIndicatorsMonth[month.number]">{{ curIndicatorsMonth[month.number]['new_clients_period_count'] | level }}</td>
                            <td v-else>0</td>
                        </template>
                        <td v-else>0</td>
                    </template>

                    <td v-if="curIndicatorsYear" class="border-left">{{ curIndicatorsYear['new_clients_period_count'] | level }}</td>
                    <td v-else class="border-left">0</td>
                </tr>
                <tr>
                    <td class="right-border">Кол-во ушедших клиентов в периоде</td>
                    <template v-for="month in months">
                        <template v-if="curIndicatorsMonth">
                            <td v-if="curIndicatorsMonth[month.number]">{{ curIndicatorsMonth[month.number]['lost_clients_period_count'] | level }}</td>
                            <td v-else>0</td>
                        </template>
                        <td v-else>0</td>
                    </template>

                    <td v-if="curIndicatorsYear" class="border-left">{{ curIndicatorsYear['lost_clients_period_count'] | level }}</td>
                    <td v-else class="border-left">0</td>
                </tr>
                <tr>
                    <td class="right-border">Коэффициент удержания</td>
                    <template v-for="month in months">
                        <template v-if="curIndicatorsMonth">
                            <td v-if="curIndicatorsMonth[month.number]">{{ curIndicatorsMonth[month.number]['customer_retention_rate'] }}</td>
                            <td v-else>0</td>
                        </template>
                        <td v-else>0</td>
                    </template>

                    <td v-if="curIndicatorsYear" class="border-left">{{ curIndicatorsYear['customer_retention_rate'] }}</td>
                    <td v-else class="border-left">0</td>
                </tr>
                <tr>
                    <td class="right-border">Оформленные сметы</td>
                    <template v-for="month in months">
                        <template v-if="curIndicatorsMonth">
                            <td v-if="curIndicatorsMonth[month.number]">{{ curIndicatorsMonth[month.number]['orders_count'] | level }}</td>
                            <td v-else>0</td>
                        </template>
                        <td v-else>0</td>
                    </template>

                    <td v-if="curIndicatorsYear" class="border-left">{{ curIndicatorsYear['orders_count'] | level }}</td>
                    <td v-else class="border-left">0</td>
                </tr>
                <tr>
                    <td class="right-border">Оформленные сметы за период</td>
                    <template v-for="month in months">
                        <template v-if="curIndicatorsMonth">
                            <td v-if="curIndicatorsMonth[month.number]">{{ curIndicatorsMonth[month.number]['orders_period_count'] | level }}</td>
                            <td v-else>0</td>
                        </template>
                        <td v-else>0</td>
                    </template>

                    <td v-if="curIndicatorsYear" class="border-left">{{ curIndicatorsYear['orders_period_count'] | level }}</td>
                    <td v-else class="border-left">0</td>
                </tr>
                <tr>
                    <td class="right-border">Коэффициент оттока</td>
                    <template v-for="month in months">
                        <template v-if="curIndicatorsMonth">
                            <td v-if="curIndicatorsMonth[month.number]">{{ curIndicatorsMonth[month.number]['churn_rate'] }}</td>
                            <td v-else>0</td>
                        </template>
                        <td v-else>0</td>
                    </template>

                    <td v-if="curIndicatorsYear" class="border-left">{{ curIndicatorsYear['churn_rate'] }}</td>
                    <td v-else class="border-left">0</td>
                </tr>
                <tr>
                    <td class="right-border">Количество покупателей из числа клиентов в периоде</td>
                    <template v-for="month in months">
                        <template v-if="curIndicatorsMonth">
                            <td v-if="curIndicatorsMonth[month.number]">{{ curIndicatorsMonth[month.number]['customers_period_count'] | level }}</td>
                            <td v-else>0</td>
                        </template>
                        <td v-else>0</td>
                    </template>

                    <td v-if="curIndicatorsYear" class="border-left">{{ curIndicatorsYear['customers_period_count'] | level }}</td>
                    <td v-else class="border-left">0</td>
                </tr>
                <tr>
                    <td class="right-border">Коэффициент закрытия лидов</td>
                    <template v-for="month in months">
                        <template v-if="curIndicatorsMonth">
                            <td v-if="curIndicatorsMonth[month.number]">{{ curIndicatorsMonth[month.number]['lead_close_rate'] }}</td>
                            <td v-else>0</td>
                        </template>
                        <td v-else>0</td>
                    </template>

                    <td v-if="curIndicatorsYear" class="border-left">{{ curIndicatorsYear['lead_close_rate'] }}</td>
                    <td v-else class="border-left">0</td>
                </tr>
                <tr>
                    <td class="right-border">Коэффициент удовлетворенности (Коэффициент повторных покупок)</td>
                    <template v-for="month in months">
                        <template v-if="curIndicatorsMonth">
                            <td v-if="curIndicatorsMonth[month.number]">{{ curIndicatorsMonth[month.number]['repeat_purchase_rate'] }}</td>
                            <td v-else>0</td>
                        </template>
                        <td v-else>0</td>
                    </template>

                    <td v-if="curIndicatorsYear" class="border-left">{{ curIndicatorsYear['repeat_purchase_rate'] }}</td>
                    <td v-else class="border-left">0</td>
                </tr>
                <tr>
                    <td class="right-border">Частота заказов</td>
                    <template v-for="month in months">
                        <template v-if="curIndicatorsMonth">
                            <td v-if="curIndicatorsMonth[month.number]">{{ curIndicatorsMonth[month.number]['purchase_frequency'] }}</td>
                            <td v-else>0</td>
                        </template>
                        <td v-else>0</td>
                    </template>

                    <td v-if="curIndicatorsYear" class="border-left">{{ curIndicatorsYear['purchase_frequency'] }}</td>
                    <td v-else class="border-left">0</td>
                </tr>
                <tr>
                    <td class="right-border">Частота заказов за период</td>
                    <template v-for="month in months">
                        <template v-if="curIndicatorsMonth">
                            <td v-if="curIndicatorsMonth[month.number]">{{ curIndicatorsMonth[month.number]['purchase_frequency_period'] }}</td>
                            <td v-else>0</td>
                        </template>
                        <td v-else>0</td>
                    </template>

                    <td v-if="curIndicatorsYear" class="border-left">{{ curIndicatorsYear['purchase_frequency_period'] }}</td>
                    <td v-else class="border-left">0</td>
                </tr>
                <tr>
                    <td class="right-border">Средний промежуток времени между покупками</td>
                    <template v-for="month in months">
                        <template v-if="curIndicatorsMonth">
                            <td v-if="curIndicatorsMonth[month.number]">{{ curIndicatorsMonth[month.number]['order_gap_analysis'] }}</td>
                            <td v-else>0</td>
                        </template>
                        <td v-else>0</td>
                    </template>

                    <td v-if="curIndicatorsYear" class="border-left">{{ curIndicatorsYear['order_gap_analysis'] }}</td>
                    <td v-else class="border-left">0</td>
                </tr>
                <tr>
                    <td class="right-border">Общая выручка</td>
                    <template v-for="month in months">
                        <template v-if="curIndicatorsMonth">
                            <td v-if="curIndicatorsMonth[month.number]">{{ curIndicatorsMonth[month.number]['orders_revenue'] | level }}</td>
                            <td v-else>0</td>
                        </template>
                        <td v-else>0</td>
                    </template>

                    <td v-if="curIndicatorsYear" class="border-left">{{ curIndicatorsYear['orders_revenue'] | level }}</td>
                    <td v-else class="border-left">0</td>
                </tr>
                <tr>
                    <td class="right-border">Общая выручка за период</td>
                    <template v-for="month in months">
                        <template v-if="curIndicatorsMonth">
                            <td v-if="curIndicatorsMonth[month.number]">{{ curIndicatorsMonth[month.number]['orders_revenue_period'] | level }}</td>
                            <td v-else>0</td>
                        </template>
                        <td v-else>0</td>
                    </template>

                    <td v-if="curIndicatorsYear" class="border-left">{{ curIndicatorsYear['orders_revenue_period'] | level }}</td>
                    <td v-else class="border-left">0</td>
                </tr>
                <tr>
                    <td class="right-border">Средний доход от клиента за период</td>
                    <template v-for="month in months">
                        <template v-if="curIndicatorsMonth">
                            <td v-if="curIndicatorsMonth[month.number]">{{ curIndicatorsMonth[month.number]['arpu'] | level }}</td>
                            <td v-else>0</td>
                        </template>
                        <td v-else>0</td>
                    </template>

                    <td v-if="curIndicatorsYear" class="border-left">{{ curIndicatorsYear['arpu'] | level }}</td>
                    <td v-else class="border-left">0</td>
                </tr>
                <tr>
                    <td class="right-border">Средний доход от платящего клиента за период</td>
                    <template v-for="month in months">
                        <template v-if="curIndicatorsMonth">
                            <td v-if="curIndicatorsMonth[month.number]">{{ curIndicatorsMonth[month.number]['arppu'] | level }}</td>
                            <td v-else>0</td>
                        </template>
                        <td v-else>0</td>
                    </template>

                    <td v-if="curIndicatorsYear" class="border-left">{{ curIndicatorsYear['arppu'] | level }}</td>
                    <td v-else class="border-left">0</td>
                </tr>
                <tr>
                    <td class="right-border">Доля платящих клиентов</td>
                    <template v-for="month in months">
                        <template v-if="curIndicatorsMonth">
                            <td v-if="curIndicatorsMonth[month.number]">{{ curIndicatorsMonth[month.number]['paying_share'] }}</td>
                            <td v-else>0</td>
                        </template>
                        <td v-else>0</td>
                    </template>

                    <td v-if="curIndicatorsYear" class="border-left">{{ curIndicatorsYear['paying_share'] }}</td>
                    <td v-else class="border-left">0</td>
                </tr>
                <tr>
                    <td class="right-border">Срок жизни по методике</td>
                    <template v-for="month in months">
                        <template v-if="curIndicatorsMonth">
                            <td v-if="curIndicatorsMonth[month.number]">{{ curIndicatorsMonth[month.number]['lifetime'] }}</td>
                            <td v-else>0</td>
                        </template>
                        <td v-else>0</td>
                    </template>

                    <td v-if="curIndicatorsYear" class="border-left">{{ curIndicatorsYear['lifetime'] }}</td>
                    <td v-else class="border-left">0</td>
                </tr>
                <tr>
                    <td class="right-border">Срок жизни по нашим расчетам</td>
                    <template v-for="month in months">
                        <template v-if="curIndicatorsMonth">
                            <td v-if="curIndicatorsMonth[month.number]">{{ curIndicatorsMonth[month.number]['lifetime_fact'] }}</td>
                            <td v-else>0</td>
                        </template>
                        <td v-else>0</td>
                    </template>

                    <td v-if="curIndicatorsYear" class="border-left">{{ curIndicatorsYear['lifetime_fact'] }}</td>
                    <td v-else class="border-left">0</td>
                </tr>
                <tr>
                    <td class="right-border">Средний чек</td>
                    <template v-for="month in months">
                        <template v-if="curIndicatorsMonth">
                            <td v-if="curIndicatorsMonth[month.number]">{{ curIndicatorsMonth[month.number]['average_order_value'] | level }}</td>
                            <td v-else>0</td>
                        </template>
                        <td v-else>0</td>
                    </template>

                    <td v-if="curIndicatorsYear" class="border-left">{{ curIndicatorsYear['average_order_value'] | level }}</td>
                    <td v-else class="border-left">0</td>
                </tr>
                <tr>
                    <td class="right-border">Средний чек за период</td>
                    <template v-for="month in months">
                        <template v-if="curIndicatorsMonth">
                            <td v-if="curIndicatorsMonth[month.number]">{{ curIndicatorsMonth[month.number]['average_order_value_period'] | level }}</td>
                            <td v-else>0</td>
                        </template>
                        <td v-else>0</td>
                    </template>

                    <td v-if="curIndicatorsYear" class="border-left">{{ curIndicatorsYear['average_order_value_period'] | level }}</td>
                    <td v-else class="border-left">0</td>
                </tr>
                <tr>
                    <td class="right-border">Ценность клиента</td>
                    <template v-for="month in months">
                        <template v-if="curIndicatorsMonth">
                            <td v-if="curIndicatorsMonth[month.number]">{{ curIndicatorsMonth[month.number]['customer_value'] | level }}</td>
                            <td v-else>0</td>
                        </template>
                        <td v-else>0</td>
                    </template>

                    <td v-if="curIndicatorsYear" class="border-left">{{ curIndicatorsYear['customer_value'] | level }}</td>
                    <td v-else class="border-left">0</td>
                </tr>
                <tr>
                    <td class="right-border">Ценность клиента за период</td>
                    <template v-for="month in months">
                        <template v-if="curIndicatorsMonth">
                            <td v-if="curIndicatorsMonth[month.number]">{{ curIndicatorsMonth[month.number]['customer_value_period'] | level }}</td>
                            <td v-else>0</td>
                        </template>
                        <td v-else>0</td>
                    </template>

                    <td v-if="curIndicatorsYear" class="border-left">{{ curIndicatorsYear['customer_value_period'] | level }}</td>
                    <td v-else class="border-left">0</td>
                </tr>
                <tr>
                    <td class="right-border">Пожизненная ценность</td>
                    <template v-for="month in months">
                        <template v-if="curIndicatorsMonth">
                            <td v-if="curIndicatorsMonth[month.number]">{{ curIndicatorsMonth[month.number]['ltv'] | level }}</td>
                            <td v-else>0</td>
                        </template>
                        <td v-else>0</td>
                    </template>

                    <td v-if="curIndicatorsYear" class="border-left">{{ curIndicatorsYear['ltv'] | level }}</td>
                    <td v-else class="border-left">0</td>
                </tr>
                <tr>
                    <td class="right-border">Пожизненная ценность на основе данных за период</td>
                    <template v-for="month in months">
                        <template v-if="curIndicatorsMonth">
                            <td v-if="curIndicatorsMonth[month.number]">{{ curIndicatorsMonth[month.number]['ltv_period'] | level }}</td>
                            <td v-else>0</td>
                        </template>
                        <td v-else>0</td>
                    </template>

                    <td v-if="curIndicatorsYear" class="border-left">{{ curIndicatorsYear['ltv_period'] | level }}</td>
                    <td v-else class="border-left">0</td>
                </tr>
                <tr>
                    <td class="right-border">Клиентский капитал</td>
                    <template v-for="month in months">
                        <template v-if="curIndicatorsMonth">
                            <td v-if="curIndicatorsMonth[month.number]">{{ curIndicatorsMonth[month.number]['customer_equity'] | level }}</td>
                            <td v-else>0</td>
                        </template>
                        <td v-else>0</td>
                    </template>

                    <td v-if="curIndicatorsYear" class="border-left">{{ curIndicatorsYear['customer_equity'] | level }}</td>
                    <td v-else class="border-left">0</td>
                </tr>
                <tr>
                    <td class="right-border">Индекс лояльности</td>
                    <template v-for="month in months">
                        <template v-if="curIndicatorsMonth">
                            <td v-if="curIndicatorsMonth[month.number]">{{ curIndicatorsMonth[month.number]['nps'] }}</td>
                            <td v-else>0</td>
                        </template>
                        <td v-else>0</td>
                    </template>

                    <td v-if="curIndicatorsYear" class="border-left">{{ curIndicatorsYear['nps'] }}</td>
                    <td v-else class="border-left">0</td>
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
                loadingYear: false,
                disabledSelect: false,
            }
        },
        computed: {
            curIndicatorsMonth() {
                return this.indicators[this.year].months;
            },
            curIndicatorsYear() {
                return this.indicators[this.year].year;
            },
        },
        methods: {
            isDisabled(month) {
                if (this.curYear == this.year) {
                    return this.curMonth < month;
                } else {
                    return false;
                }
            },
            changeIndicatorsForYear() {
                if(! this.indicators[this.year]) {
                    Vue.set(this.indicators, this.year, {});
                }
            },
            getIndicatorsForMonth(number) {
                if (! this.isDisabled(number)) {
                    var year = this.year;
                    var month = this.months.find(obj => obj.number == number);
                    // console.log(month);
                    month.loading = true;
                    this.disabledSelect = true;
                    axios
                        .post('/admin/clients_indicators/compute/month', {
                            month: number,
                            year: year
                        })
                        .then(response => {
                            // console.log(response.data);
                            if (! this.indicators[year].months) {
                                 Vue.set(this.indicators[year], 'months', {});
                            }
                            Vue.set(this.indicators[year].months, number, response.data);
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
            getIndicatorsForYear() {
                var year = this.year;
                this.loadingYear = true;
                this.disabledSelect = true;
                axios
                    .post('/admin/clients_indicators/compute/year', {
                        year: year
                    })
                    .then(response => {
                        Vue.set(this.indicators[year], 'year', response.data);
                        this.loadingYear = false;
                        this.disabledSelect = false;
                    })
                    .catch(error => {
                        console.log(error);
                        this.loadingYear = false;
                        this.disabledSelect = false;
                    });
            },
        },
        filters: {
            roundToTwo: function (value) {
                return Math.trunc(parseFloat(Number(value).toFixed(2)) * 100) / 100;
            },
            // Создает разделители разрядов в строке с числами
            level: function (value) {
                return parseInt(value).toLocaleString();
            },

            // Отбраcывает дробную часть в строке с числами
            onlyInteger(value) {
                return Math.floor(value);
            },
        },
    }
</script>
