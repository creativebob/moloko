<template>
    <div class="grid-x grid-padding-x">
        <div class="cell small-12 medium-6 large-9 checkbox">
            <input
                type="hidden"
                name="is_discount"
                value="0"
            >
            <input
                type="checkbox"
                name="is_discount"
                id="checkbox-is_discount"
                value="1"
                :checked="item.is_discount == 1"
            >
            <label for="checkbox-is_discount">
                <span>Скидки включены</span>
            </label>
        </div>
            <div class="cell -small-12 medium-6 large-3">

                <items-list-component
                    name="discounts"
                    :items="discounts"
                    :actual-items="actualDiscounts"
                    @add="addItem"
                    @remove="removeItem"

                ></items-list-component>
            </div>
            <table
                v-if="actualDiscounts.length"
                class="table-compositions"
            >
                <thead>
                <tr>
                    <th class="td-drop"></th>
                    <th>№:</th>
                    <th>Название:</th>
                    <th>Описание:</th>
                    <th>Проценты:</th>
                    <th>Валюта:</th>
                    <th>Дата начала:</th>
                    <th>Дата окончания:</th>
                    <th></th>
                </tr>
                </thead>

                <draggable
                    v-model="actualDiscounts"
                    tag="tbody"
                    id="table-prices"
                    handle=".td-drop"
                >
                    <discount-component
                        v-for="(item, index) in actualDiscounts"
                        :item="item"
                        :index="index"
                        :key="item.id"
                        @remove="removeItem"
                    ></discount-component>
                </draggable>

            </table>
    </div>
</template>

<script>
    import draggable from 'vuedraggable'

    export default {
        components: {
            'items-list-component': require('../dropdowns/items/ItemsListComponent'),
            'discount-component': require('./DiscountComponent'),
            draggable,
        },
        props: {
            discounts: Array,
            item: Object,
            entity: String
        },
        data() {
            return {
                actualDiscounts: this.item.discounts,
            }
        },
        methods: {

            addItem(item) {
                this.actualDiscounts.push(item);
            },
            removeItem(id) {
                let index = this.actualDiscounts.findIndex(item => item.id == id);
                this.actualDiscounts.splice(index, 1);
            },
        },
    }
</script>
