<template>
    <div>
        <table class="table-estimate lead-estimate" id="table-estimate_goods_items">

            <caption>Товары</caption>

            <thead>
            <tr>
                <th>Наименование</th>
<!--                <th v-if="settings.length">Склад</th>-->
                <th>Цена</th>
                <th>Кол-во</th>
                <!--                                        <th>Себестоимость</th>
                                                            <th>ДопРасх</th>
                                                            <th>Наценка</th> -->
                <th class="th-amount">Сумма</th>
                <th class="th-delete"></th>
                <th
                    v-if="settings.length && isRegistered"
                    class="th-action"
                >

                    <span
                        v-if="isReserved"
                        @click="unreserveEstimateItems"
                        class="button-to-reserve"
                        title="Снять все с резерва!"
                    ></span>
                    <span
                        v-else
                        @click="reserveEstimateItems"
                        class="button-to-reserve"
                        title="Зарезервировать все!"
                    ></span>

                </th>
            </tr>
            </thead>

            <tbody>
                <estimates-goods-item-component
                    v-for="(item, index) in items"
                    :item="item"
                    :index="index"
                    :key="item.id"
                    :is-registered="isRegistered"
                    :settings="settings"
                    :stocks="stocks"
                    @open-modal-remove="openModal(item, index)"
                    @update="updateItem"
                ></estimates-goods-item-component>
            </tbody>

            <tfoot>
                <tr>
                    <td colspan="4" class="text-right">Итого:</td>
                    <td>{{ itemsAmount | roundToTwo | level }}</td>
                    <td colspan="2"></td>
                </tr>
                <tr v-if="discountPercent > 0">
                    <td colspan="4" class="text-right">Итого со скидкой ({{ discountPercent }}%):</td>
                    <td>{{ itemsTotal | roundToTwo | level }}</td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>

        </table>

        <div class="reveal rev-small" id="delete-estimates_goods_item" data-reveal>
            <div class="grid-x">
                <div class="small-12 cell modal-title">
                    <h5>Удаление</h5>
                </div>
            </div>
            <div class="grid-x align-center modal-content ">
                <div class="small-10 cell text-center">
                    <p>Удаляем "{{ itemName }}", вы уверены?</p>
                </div>
            </div>
            <div class="grid-x align-center grid-padding-x">
                <div class="small-6 medium-4 cell">
                    <button
                        @click.prevent="deleteItem"
                        data-close
                        class="button modal-button"
                        type="submit"
                    >Удалить</button>
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
            'estimates-goods-item-component': require('./EstimatesGoodsItemComponent.vue')
        },
        props: {
            items: Array,
            settings: Array,
            stocks: Array,
        },
        data() {
            return {
                id: null,

                count: null,
                cost: null,
                discountPercent: Number(this.$store.state.estimate.estimate.discount_percent),

                item: null,
                itemName: null,
                itemIndex: null,

                isRegistered: this.$store.state.estimate.estimate.is_registered === 1,
            }
        },
        computed: {
            estimate() {
                return this.$store.state.estimate.estimate;
            },
            itemsAmount() {
                return this.$store.getters.goodsItemsAmount;
            },
            itemsTotal() {
                return this.$store.getters.goodsItemsTotal;
            },
            showButtonReserved() {
                return this.estimate.is_reserved === 0;
            },
            isReserved() {
                let result = [];
                result = this.$store.state.estimate.goodsItems.filter(item => {
                    if (item.reserve !== null) {
                        if (item.reserve.count > 0) {
                            return item;
                        }
                    }
                });
                return result.length > 0;
            }
        },
        methods: {
            changeCount: function(value) {
                this.count = value;
            },
            openModal(item, index) {
                this.itemIndex = index;
                this.item = item;
                this.itemName = item.product.article.name;
            },
            // changeCost: function(value) {
            // 	this.cost = value;
            // },
            // checkChange: function () {
            // 	this.change = false;
            // },
            // setId: function (id) {
            // 	this.id = id;
            // 	if (id != null) {
            // 		this.categoriesItems.filter(item => {
            // 			if (item.id === id && item.entity_id === this.entity_id) {
            //
            // 				// Смотрим в чем принимать
            // 				if (item.article.package_status === 1) {
            // 					this.itemUnit = item.article.package_abbreviation;
            // 				} else {
            // 					this.itemUnit = item.article.unit.abbreviation;
            // 				}
            //
            // 				// Смотрим производителя
            // 				if (item.article.manufacturer_id != null) {
            // 					this.itemManufacturer = item.article.manufacturer_id;
            // 				}
            // 			}
            // 		});
            // 	} else {
            // 		this.itemUnit = null;
            // 		this.itemManufacturer = null;
            // 	}
            // },
            updateItem: function(item) {
                this.$store.commit('UPDATE_GOODS_ITEM', item);
            },
            deleteItem() {
                this.$store.dispatch('REMOVE_GOODS_ITEM_FROM_ESTIMATE', this.item.id);
                $('#delete-estimates_goods_item').foundation('close');
            },
            reserveEstimateItems() {
                axios
                    .post('/admin/estimates/' + this.estimate.id + '/reserving')
                    .then(response => {
                        console.log(response.data);
                        if (response.data.msg.length > 0) {
                            let msg = '';
                            response.data.msg.forEach(item => {
                                if (item !== null) {
                                    msg = msg + '- ' + item + '\r\n';
                                }
                            });
                            if (msg !== '') {
                                alert(msg);
                            }
                        }
                        this.$store.commit('UPDATE_GOODS_ITEMS', response.data.items);
                    })
                    .catch(error => {
                        console.log(error)
                    });
            },
            unreserveEstimateItems() {
                axios
                    .post('/admin/estimates/' + this.estimate.id + '/unreserving')
                    .then(response => {
                        console.log(response.data);
                        if (response.data.msg.length > 0) {
                            let msg = '';
                            response.data.msg.forEach(item => {
                                if (item !== null) {
                                    msg = msg + '- ' + item + '\r\n';
                                }
                            });
                            if (msg !== '') {
                                alert(msg);
                            }
                        }
                        this.$store.commit('UPDATE_GOODS_ITEMS', response.data.items);
                    })
                    .catch(error => {
                        console.log(error)
                    });
            },
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
