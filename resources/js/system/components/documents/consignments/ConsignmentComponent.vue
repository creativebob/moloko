<template>

    <table class="table-invoice">
        <thead>
        <tr>
            <th>№</th>
            <th>Тип:</th>
            <th>Наименование позиции:</th>
            <th>Производитель</th>
            <th>Кол-во:</th>
            <th>Ед. изм.:</th>
            <th>Цена:</th>
            <th>Валюта:</th>
            <th>Сумма:</th>
            <!--			<th>% НДС:</th>-->
            <!--			<th>НДС:</th>-->
            <!--			<th>Всего:</th>-->
            <th
                v-if="!isReceipted"
            ></th>
        </tr>
        </thead>

        <tbody id="table-raws">

        <consignments-item-component
            v-for="(item, index) in itemsList"
            :item="item"
            :index="index"
            :key="item.id"
            :is-receipted="isReceipted"
            @update="updateItem"
            @remove="deleteItem(index)"
        ></consignments-item-component>

        <tr
            v-if="!isReceipted"
            class="tr-add"
        >
            <td>{{ items.length + 1}}</td>
            <td>
                <select
                    v-model="entity_id"
                    name="entity_id"
                    @change="changeEntity"
                >
                    <option
                        v-for="entity in entities"
                        :value="entity.id"
                        :selected="entity.id === selectedEntity"
                    >{{ entity.name }}
                    </option>
                </select>
            </td>
            <td>
                <select-categories-component
                    :select-categories="selectCategories"
                    :select-categories-items="selectCategoriesItems"
                    :change="change"
                    @set-id="setId"
                    @check-change="checkChange"
                ></select-categories-component>
            </td>
            <td>

                <template v-if="id != null">
						<span v-if="itemManufacturer != null ">
							{{ manufacturer[0].company.name }}
						</span>

                    <template v-else>
                        <select

                            v-model="manufacturerId"
                            name="entity_id"
                        >
                            <option
                                v-for="manufacturer in manufacturers"
                                :value="manufacturer.id"
                            >{{ manufacturer.company.name}}
                            </option>
                        </select>
                    </template>
                </template>

            </td>
            <td>
                <!--					<input-->
                <!--							v-model="count"-->
                <!--							name="count"-->
                <!--							type="number"-->
                <!--					>-->
                <digit-component
                    :value="count"
                    :decimal-place="0"
                    v-model="count"
                    ref="countComponent"
                ></digit-component>
            </td>
            <td>{{ itemUnit }}</td>
            <td>
                <digit-component
                    :value="cost"
                    v-model="cost"
                    ref="costComponent"
                ></digit-component>
            </td>
            <td>
                <label v-if="currencies.length > 1">
                    <select
                        v-model="currencyId"
                    >
                        <option
                            v-for="currency in currencies"
                            :value="currency.id"
                        >{{ currency.name }}
                        </option>
                    </select>
                </label>
                <template v-else>
                    {{ currencies[0].name }}
                </template>
            </td>
            <td>
                <span>{{ totalItemSum | roundToTwo }}</span>
            </td>
            <!--			<td>-->
            <!--				<select v-model="vat_rate" name="vat_rate">-->
            <!--					<option value="0">Без НДС</option>-->
            <!--					<option value="10">10</option>-->
            <!--					<option value="20">20</option>-->
            <!--				</select>-->
            <!--			</td>-->
            <!--			<td><span> {{ cost * count_item * vat_rate / 100 | roundToTwo }} </span></td>-->
            <!--			<td><span> {{ (count_item * cost) + (count_item * cost * vat_rate / 100) | roundToTwo }} </span></td>-->
            <td>
                <a
                    @click="addItem"
                    class="button tiny"
                    :disabled="isDisabled"
                >Добавить</a>
            </td>
        </tr>

        </tbody>

        <tfoot>
        <tr>
            <td colspan="7">Итого:</td>
            <td></td>
            <td><span class="font-accent">{{ totalItemsCost | roundToTwo | level }}</span></td>
            <td
                v-if="!isReceipted"
            ></td>
        </tr>
        </tfoot>
    </table>

</template>

<script>
    export default {
        components: {
            'consignments-item-component': require('./ConsignmentsItemComponent'),
            'select-categories-component': require('../../common/selects_categories/SelectCategoriesComponent'),
            'digit-component': require('../../inputs/DigitComponent'),
        },
        props: {
            consignment: Object,
            selectData: Object,
            currencies: {
                type: Array,
                default: [
                    {
                        id: 1,
                        name: 'Рубль',
                    }
                ],
            }
        },
        data() {
            return {
                // Сущности
                entities: this.selectData.entities,
                selectedEntity: this.selectData.entities[0].id,
                entity_id: this.selectData.entities[0].id,

                //
                items: this.consignment.items,
                id: null,
                count: 0,
                cost: 0,

                // Категории для компонента выбора
                categories: this.selectData.categories,
                categoriesItems: this.selectData.items,
                change: false,
                itemUnit: null,

                // Производители
                manufacturers: this.selectData.manufacturers,
                itemManufacturer: null,
                manufacturerId: null,

                // Валюте
                currencyId: this.currencies[0].id,
            }
        },
        computed: {
            totalItemSum() {
                return this.count * parseFloat(this.cost);
            },
            isDisabled() {
                return this.id == null || this.cost == 0 || this.count == 0;
            },
            itemsList() {
                return this.items;
            },
            totalItemsCount() {
                return this.items.length;
            },
            totalItemsCost() {
                let cost = 0;
                this.items.forEach(function (item) {
                    return cost += Number(item.amount)
                });
                return cost;
            },

            // Списки для компонента выбора
            selectCategories() {
                return this.categories.filter(item => {
                    return item.entity_id === this.entity_id
                })
            },
            selectCategoriesItems() {
                return this.categoriesItems.filter(item => {
                    return item.entity_id === this.entity_id
                })
            },
            isReceipted() {
                return this.consignment.receipted_at;
            },
            manufacturer() {
                return this.manufacturers.filter(item => {
                    if (item.id === this.itemManufacturer) {
                        this.manufacturerId = item.id;
                        return item;
                    }

                })
            }
        },
        methods: {
            changeEntity() {
                this.change = true;

                let count = 0;
                this.categories.filter(item => {
                    if (item.entity_id === this.entity_id) {
                        count++
                    }
                });

                if (count === 0) {
                    axios
                        .post('/admin/consignments/categories', {
                            entity_id: this.entity_id,
                        })
                        .then(response => {
                            this.categories = this.categories.concat(response.data.categories);
                            this.categoriesItems = this.categoriesItems.concat(response.data.items);
                        })
                        .catch(error => {
                            console.log(error);
                        })
                }

            },
            checkChange() {
                this.change = false;
            },
            setId(id) {
                this.id = id;
                if (id != null) {
                    this.categoriesItems.filter(item => {
                        if (item.id === id && item.entity_id === this.entity_id) {

                            // Смотрим в чем принимать
                            if (item.article.package_status === 1) {
                                this.itemUnit = item.article.package_abbreviation;
                            } else {
                                this.itemUnit = item.article.unit.abbreviation;
                            }

                            // Смотрим производителя
                            if (item.article.manufacturer_id != null) {
                                this.itemManufacturer = item.article.manufacturer_id;
                            }
                        }
                    });
                } else {
                    this.itemUnit = null;
                    this.itemManufacturer = null;
                }
            },
            addItem() {
                if (!this.isDisabled) {
                    this.disabledButton = true;
                    axios
                        .post('/admin/consignments_items', {
                            consignment_id: this.consignment.id,
                            cmv_id: this.id,
                            entity_id: this.entity_id,
                            count: this.count,
                            cost: this.cost,
                            manufacturer_id: this.manufacturerId,
                            currency_id: this.currencyId,
                        })
                        .then(response => {
                            this.items.push(response.data);

                            this.id = null;
                            this.change = true;
                            this.manufacturerId = null;
                            this.itemManufacturer = null;

                            this.count = 0;
                            this.$refs.countComponent.update(this.count);
                            this.cost = 0;
                            this.$refs.costComponent.update(this.cost);
                        })
                        .catch(error => {
                            console.log(error)
                        });
                }
            },
            updateItem(item, index) {
                Vue.set(this.items, index, item);
            },
            deleteItem(index) {
                this.items.splice(index, 1);
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
        },
    }
</script>
