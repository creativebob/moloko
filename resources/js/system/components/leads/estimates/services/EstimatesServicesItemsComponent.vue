<template>
    <div>

        <table class="table-estimate lead-estimate" id="table-estimate_services_items">

            <caption>Услуги</caption>

            <thead>
            <tr>
                <th>Наименование</th>
                <th>Цена</th>
                <th>Кол-во</th>
                <th class="th-amount">Сумма</th>
                <th class="th-delete"></th>
                <th class="th-action"></th>
            </tr>
            </thead>

            <tbody>
            <estimates-services-item-component
                v-for="(item, index) in items"
                :item="item"
                :index="index"
                :key="item.id"
                :is-registered="isRegistered"
                @open-modal-remove="openModal(item, index)"
                @update="updateItem"
            ></estimates-services-item-component>
            </tbody>

            <tfoot>
            <tr>
                <td colspan="3" class="text-right">Итого:</td>
                <td>{{ itemsAmount | roundToTwo | level }}</td>
                <td colspan="2"></td>
            </tr>
            <tr v-if="discountPercent > 0">
                <td colspan="3" class="text-right">Итого со скидкой ({{ discountPercent }}%):</td>
                <td>{{ titemsTotal | roundToTwo | level }}</td>
                <td colspan="2"></td>
            </tr>
            </tfoot>

        </table>

        <div class="reveal rev-small" id="delete-estimates_services_item" data-reveal>
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
            'estimates-services-item-component': require('./EstimatesServicesItemComponent.vue')
        },
        props: {
            items: Array,
        },
        data() {
            return {
                id: null,

                count: null,
                cost: null,
                discountPercent: Number(this.$store.state.lead.estimate.discount_percent),

                item: null,
                itemName: null,
                itemIndex: null,

                isRegistered: this.$store.state.lead.estimate.is_registered === 1,
            }
        },
        computed: {
            estimate() {
                return this.$store.state.lead.estimate;
            },
            itemsAmount() {
                return this.$store.getters.servicesItemsAmount;
            },
            itemsTotal() {
                return this.$store.getters.servicesItemsTotal;
            },
            showButtonReserved() {
                return this.estimate.is_reserved === 0;
            },
            isReserved() {
                let result = [];
                result = this.$store.state.lead.servicesItems.filter(item => {
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
                this.itemName = item.product.process.name;
            },
            updateItem: function(item) {
                this.$store.commit('UPDATE_SERVICES_ITEM', item);
            },
            deleteItem() {
                this.$store.dispatch('REMOVE_SERVICES_ITEM_FROM_ESTIMATE', this.item.id);
                $('#delete-estimates_services_item').foundation('close');
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
