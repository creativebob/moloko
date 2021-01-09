<template>
    <div class="grid-x grid-padding-x">
        <div class="small-12 medium-9 cell">

            <div class="grid-x grid-padding-x">

                <div class="small-12 medium-6 large-9 cell">

                </div>

                <div class="small-12 medium-6 large-3 cell">

                    <categories-list-component
                        :categories="categories"
                        :items="items"
                        :actual-items="actualItems"
                        :name="name"
                        @add="addItem"
                        @remove="removeItem"
                    ></categories-list-component>

                </div>
            </div>

            <div class="grid-x grid-padding-x">
                <div class="small-12 cell">
                    <table class="table-compositions">

                        <thead>
                        <tr>
                            <th>п/п</th>
                            <th>Категория</th>
                            <th>Название</th>
                            <th>Описание</th>
                            <th>Ед. изм.</th>
                            <th></th>
                        </tr>
                        </thead>

                        <tbody
                            :id="'table-' + name"
                        >

                        <template v-if="actualItems && actualItems.length">
                            <preset-component
                                v-for="(item, index) in actualItems"
                                :item="item"
                                :index="index"
                                :name="name"
                                :key="item.id"
                                @remove="removeItem"
                            ></preset-component>
                        </template>

                        </tbody>

                    </table>
                </div>
            </div>

        </div>
    </div>
</template>

<script>
    export default {
        components: {
            'categories-list-component': require('../common/CategoriesWithItemsListComponent'),
            'preset-component': require('./PresetComponent'),
        },
        props: {
            categories: Array,
            items: Array,
            itemItems: Array,
            name: String
        },
        data() {
            return {
                curItems: this.itemItems,
                text: null,
                search: false,
                error: false,
                results: [],
            }
        },
        computed: {
            actualItems() {
                return this.curItems;
            },
        },
        methods: {
            addItem(item) {
                this.curItems.push(item);
            },
            removeItem(id) {
                let index = this.curItems.findIndex(item => item.id == id);
                this.curItems.splice(index, 1);
            },
        }
    }
</script>
