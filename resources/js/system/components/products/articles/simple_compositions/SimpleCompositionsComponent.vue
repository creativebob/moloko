<template>
    <div class="grid-x grid-padding-x">
        <div class="small-12 medium-9 cell">

            <div class="grid-x grid-padding-x">

                <div class="small-12 medium-6 large-9 cell cmv-indicators">
                    <div class="grid-x grid-margin-x">
                        <div class="cell shrink">
                            <span class="indicator-name">Вес: </span><span class="indicators_total">{{ totalWeight }}</span> <span>гр.</span>
                        </div>
                        <div class="cell auto">
                            <span class="indicator-name">Себестоимость: </span><span class="indicators_total">{{ totalCost }}</span> <span>руб.</span>
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
                            <th>п/п</th>
                            <th>Категория:</th>
                            <th>Продукт:</th>
                            <th>Кол-во:</th>
                            <th>Вес, гр.</th>
                            <th>Объем, л.</th>
                            <th>Стоимость по накладным, руб.</th>
                            <th>Стоимость по умолчанию, руб.</th>
                            <th></th>
                        </tr>
                        </thead>

                        <tbody
                            :id="'table-' + name"
                        >

                            <template v-if="actualItems && actualItems.length">
                                <simple-composition-component
                                    v-for="(item, index) in actualItems"
                                    :item="item"
                                    :index="index"
                                    :name="name"
                                    :key="item.id"
                                    @open-modal="openModal"
                                    :disabled="disabled"
                                    @update="updateItem"
                                ></simple-composition-component>
                            </template>

                        </tbody>

                        <tfoot>
                            <tr>
                                <td colspan="4"></td>
                                <td>
                                    <span>{{ totalWeight }}</span> <span></span>
                                </td>
                                <td>
                                    <span>{{ totalVolume }}</span> <span></span>
                                </td>
                                <td>
                                    <span class="item-total-cost">{{ totalCost }}</span>
                                </td>
                                <td>
                                    <span class="item-total-cost-default">{{ totalCostDefault }}</span>
                                </td>
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
    export default {
        components: {
            'simple-composition-component': require('./SimpleCompositionComponent'),
            'modal-remove-component': require('./ModalRemoveComponent'),
            'categories-list-component': require('../common/CategoriesWithItemsListComponent'),
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
                curItems: this.itemItems,
                text: null,
                search: false,
                error: false,
                results: [],
                removingItem: {
                    article: {
                        name: null
                    }
                },
                totalWeight: 0,
                totalVolume: 0,
                totalCost: 0,
                totalCostDefault: 0,
            }
        },
        created() {

            let totalWeight = 0,
                totalVolume = 0,
                totalCost = 0,
                totalCostDefault = 0;

            this.curItems.forEach(item => {

                let weight = 0;
                let volume = 0;

                weight = parseFloat(item.article.weight * item.pivot.useful * 1000).toFixed(2);
                volume = parseFloat(item.article.volume * item.pivot.useful * 1000).toFixed(2);

                totalWeight = parseFloat(totalWeight) + parseFloat(weight);
                item.totalWeight = weight;

                totalVolume = parseFloat(totalVolume) + parseFloat(volume);
                item.totalVolume = volume;

                let cost = 0;
                let costDefault = 0;

                cost = parseFloat(item.article.cost_default * item.pivot.value * item.article.unit.ratio).toFixed(2);
                costDefault = parseFloat(item.article.cost_default * item.pivot.value * item.article.unit.ratio).toFixed(2);

                totalCost = parseFloat(totalCost) + parseFloat(cost);
                item.totalCost = cost;

                totalCostDefault = parseFloat(totalCostDefault) + parseFloat(costDefault);
                item.totalCostDefault = costDefault;
            });

            this.totalWeight = totalWeight.toFixed(2);
            this.totalVolume = totalVolume.toFixed(2);
            this.totalCost = totalCost.toFixed(2);
            this.totalCostDefault = totalCostDefault.toFixed(2);

            this.updateStore();
        },

        computed: {
            actualItems() {
                return this.curItems;
            },
            composition() {
                return {
                    name: this.name,
                    items: this.actualItems
                };
            }
        },
        methods: {
            setTotalWeight() {
                let weight = 0;
                this.curItems.forEach(item => {
                    weight += parseFloat(item.totalWeight);
                });
                this.totalWeight = weight.toFixed(2);
            },
            setTotalVolume() {
                let volume = 0;
                this.curItems.forEach(item => {
                    volume += parseFloat(item.totalVolume);
                });
                this.totalVolume = volume.toFixed(2);
            },
            setTotalCost() {
                let cost = 0;
                this.curItems.forEach(item => {
                    cost += parseFloat(item.totalCost);
                });
                this.totalCost = cost.toFixed(2);
            },
            setTotalCostDefault() {
                let costDefault = 0;
                this.curItems.forEach(item => {
                    costDefault += parseFloat(item.totalCostDefault);
                });
                this.totalCostDefault = costDefault.toFixed(2);
            },
            addItem(item) {
                item.pivot = {
                    value: 0,
                    useful: 0,
                    waste: 0,
                    is_manual_waste: 0,
                };
                item.totalWeight = parseFloat('0').toFixed(2);
                item.totalVolume = parseFloat('0').toFixed(2);
                item.totalCost = parseFloat('0').toFixed(2);
                item.totalCostDefault = parseFloat('0').toFixed(2);

                this.curItems.push(item);
                this.setTotalWeight();
                this.setTotalVolume();
                this.setTotalCost();
                this.setTotalCostDefault();
                this.updateStore();
            },
            updateItem(item){
                let found = this.curItems.find(obj => obj.id == item.id);
                Vue.set(found, 'item', item);
                this.setTotalWeight();
                this.setTotalVolume();
                this.setTotalCost();
                this.setTotalCostDefault();
                this.updateStore();
            },
            removeItem(id) {
                let index = this.curItems.findIndex(item => item.id == id);
                this.curItems.splice(index, 1);
                this.$refs.categoriesListComponent.clear();
                this.setTotalWeight();
                this.setTotalVolume();
                this.setTotalCost();
                this.setTotalCostDefault();
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
