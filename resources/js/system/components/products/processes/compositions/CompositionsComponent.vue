<template>
    <div class="grid-x grid-padding-x">
        <div class="small-12 medium-9 cell">

            <div class="grid-x grid-padding-x">

                <div class="small-12 medium-6 large-9 cell cmv-indicators">
                    <div class="grid-x grid-margin-x">
                        <div class="cell shrink">
                            <span class="indicator-name">Продолжительность: </span><span class="indicators_total">{{ totalLength }}</span> <span>сек.</span>
                        </div>
                        <div class="cell auto">
                        </div>
                    </div>
                </div>

                <div v-if="! disabled" class="small-12 medium-6 large-3 cell">
                    <categories-list-component
                        :categories="categories"
                        :items="items"
                        :actual-items="actualItems"
                        :name="name"
                        @add="addItem"
                        @remove="removeItem"
                        ref="categoriesListComponent"
                    ></categories-list-component>
                </div>

            </div>

            <div class="grid-x grid-padding-x">
                <div class="small-12 cell">
                    <table class="table-compositions">

                        <thead>
                        <tr>
                            <th></th>
                            <th>п/п</th>
                            <th>Категория:</th>
                            <th>Продукт:</th>
                            <th>Кол-во:</th>
<!--                            <th>Использование:</th>-->
<!--                            <th>Отход:</th>-->
<!--                            <th>Остаток:</th>-->
<!--                            <th>Операция над остатком:</th>-->
                            <th>Продолжительность</th>
<!--                            <th>Себестоимость</th>-->
                            <th></th>
                        </tr>
                        </thead>

                            <draggable
                                v-model="actualItems"
                                tag="tbody"
                                :id="'table-' + name"
                                handle=".td-drop"
                            >
                                <composition-component
                                    v-for="(item, index) in actualItems"
                                    :item="item"
                                    :index="index"
                                    :name="name"
                                    :key="item.id"
                                    @open-modal="openModal"
                                    :disabled="disabled"
                                    @update="updateItem"
                                ></composition-component>
                            </draggable>

                        <tfoot>
                            <tr>
                                <td colspan="5"></td>
                                <td>
                                    <span>{{ totalLength }}</span> <span>гр.</span>
                                </td>
<!--                                <td>-->
<!--                                    <span>{{ totalCost }}</span> <span>руб.</span>-->
<!--                                </td>-->
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <modal-remove-component
                :item="removingItem"
                :name="name"
                @remove="removeItem"
            ></modal-remove-component>

        </div>
    </div>
</template>

<script>
import draggable from 'vuedraggable'

    export default {
        components: {
            'composition-component': require('./CompositionComponent'),
            'modal-remove-component': require('./ModalRemoveComponent'),
            'categories-list-component': require('../common/CategoriesWithItemsListComponent'),
            draggable,
        },
        props: {
            categories: Array,
            items: Array,
            itemItems: Array,
            name: String,
            disabled: {
                type: Boolean,
                default: false
            }
        },
        data() {
            return {
                actualItems: this.itemItems,
                text: null,
                search: false,
                error: false,
                results: [],
                removingItem: {
                    process: {
                        name: null
                    }
                },
                totalLength: 0,
                totalCost: 0
            }
        },
        created() {
            let totalLength = 0,
                totalCost = 0;

            // this.actualItems.forEach(item => {
            //     let length = 0;
            //     if (this.name == 'goods') {
            //         length = parseFloat(item.process.length * 1000 * item.pivot.useful).toFixed(2);
            //     } else {
            //         length = parseFloat(item.length * 1000 * item.pivot.useful).toFixed(2);
            //     }
            //     totalLength = parseFloat(totalLength) + parseFloat(length);
            //     item.totalLength = length;
            //
            //     let cost = 0;
            //     if (this.name == 'attachments' || this.name == 'containers') {
            //         cost = parseFloat(item.cost_unit * item.pivot.useful).toFixed(2);
            //     } else if (this.name == 'raws') {
            //         cost = parseFloat(item.cost_portion * item.pivot.useful).toFixed(2);
            //     } else if (this.name == 'goods') {
            //         cost = parseFloat(item.process.cost_default * item.pivot.useful).toFixed(2);
            //     } else {
            //         cost = parseFloat(item.process.cost_default).toFixed(2);
            //     }
            //     totalCost = parseFloat(totalCost) + parseFloat(cost);
            //     item.totalCost = cost;
            // });

            this.totalLength = totalLength.toFixed(2);
            this.totalCost = totalCost.toFixed(2);

            this.updateStore();
        },
        computed: {
            // actualItems() {
            //     return this.actualItems;
            // },
            composition() {
                return {
                    name: this.name,
                    items: this.actualItems
                };
            }
        },
        methods: {
            setTotalLength() {
                let length = 0;
                this.actualItems.forEach(item => {
                    length += parseFloat(item.totalLength);
                });
                this.totalLength = length.toFixed(2);
            },
            setTotalCost() {
                let cost = 0;
                this.actualItems.forEach(item => {
                    cost += parseFloat(item.totalCost);
                });
                this.totalCost = cost.toFixed(2);
            },
            addItem(item) {
                item.pivot = {
                    value: 0,
                    useful: 0
                };
                item.totalLength = parseFloat('0').toFixed(2);
                item.totalCost = parseFloat('0').toFixed(2);

                this.actualItems.push(item);
                this.setTotalLength();
                this.setTotalCost();
                this.updateStore();
            },
            updateItem(item){
                let found = this.actualItems.find(obj => obj.id == item.id);
                Vue.set(found, 'item', item);
                this.setTotalLength();
                this.setTotalCost();
                this.updateStore();
            },
            removeItem(id) {
                let index = this.actualItems.findIndex(item => item.id == id);
                this.actualItems.splice(index, 1);
                this.$refs.categoriesListComponent.clear();
                this.setTotalLength();
                this.setTotalCost();
                this.updateStore();
            },
            openModal(item) {
                this.removingItem = item;
            },
            updateStore() {
                this.$store.commit('SET_COMPOSITION', this.composition);
            }
        }
    }
</script>
