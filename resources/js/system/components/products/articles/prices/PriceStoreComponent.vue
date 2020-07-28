<template>
    <div
        v-if="catalogs.length"
        class="grid-x grid-padding-x"
        id="form-prices_goods"
    >

        <div class="cell small-12">
            <div class="grid-x grid-padding-x">
                <div class="medium-3 cell">
                    <label>Каталог
                        <select
                            v-model="catalogId"
                            @change="changeCatalog"

                        >
                            <option
                                v-for="catalog in catalogs"
                                :value="catalog.id"
                            >{{ catalog.name }}</option>

                        </select>
                    </label>
                </div>
                <div class="cell medium-3">
                    <label>Пункты каталога
                        <select
                            v-model="catalogItem"
                            @change="checkDisabled"
                        >
                            <option
                                v-for="item in catalogItemsList"
                                :value="item"
                            >{{ getCount(item.level) }}{{ item.name }}</option>

                        </select>
                    </label>
                </div>
                <div class="cell medium-3">
                    <label>Филиал
                        <select
                            v-model="filialId"
                            @change="checkDisabled"
                        >
                            <option
                                v-for="filial in filialsList"
                                :value="filial.id"
                            >{{ filial.name }}</option>

                        </select>
                    </label>
                </div>
                <div class="cell medium-3">
                    <label>Цена
                        <div class="input-group">
                            <input
                                type="number"
                                v-model="price"
                                class="input-group-field"
                                @keydown.enter.prevent="addPrice"
                            >
                            <div class="input-group-button">
                                <a
                                    @click="addPrice"
                                    class="button"
                                >+</a>
                            </div>
                        </div>


                    </label>
                    <span
                        v-if="error"

                    >Такой прайс существует!</span>

                </div>

                <!--                    @php-->
                <!--                    $currencies = auth()->user()->company->currencies;-->
                <!--                    @endphp-->

                <!--                    @if($currencies->isNotEmpty())-->
                <!--                    @if($currencies->count() > 1)-->
                <!--                    <div class="medium-3 cell">-->
                <!--                        <label>Валюта-->
                <!--                            {!! Form::select('currency_id', $currencies->pluck('name', 'id'), $currencies->first()->id, ['required']) !!}-->
                <!--                        </label>-->
                <!--                        <span class="form-error">Введите цену!</span>-->
                <!--                    </div>-->
                <!--                    @else-->
                <!--                    {!! Form::hidden('currency_id', $currencies->first()->id) !!}-->
                <!--                    @endif-->
                <!--                    @else-->
                <!--                    {!! Form::hidden('currency_id', 1) !!}-->
                <!--                    @endif-->

            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            catalogsData: Object,
            goodsId: Number,
            prices: Array,
            disabledFilials: Array,
        },
        data() {
            return {
                catalogs: [],

                price: null,
                catalogId: null,
                catalogItem: {},
                filialId: null,
                currencyId: null,

                catalogsItems: [],
                filials: [],
                currencies: [],
                error: false
            }
        },

        mounted() {
            if (this.catalogsData.catalogs.length) {
                this.catalogs = this.catalogsData.catalogs;
                this.catalogId = this.catalogs[0].id;

                if (this.catalogs[0].items_tree.length) {
                    // this.catalogsItems = this.catalogsData.catalogsItems;
                    this.catalogItem = this.catalogs[0].items_tree[0];
                }

                if (this.catalogs[0].filials.length) {
                    // this.filials = this.catalogsData.filials;
                    this.filialId = this.catalogs[0].filials[0].id;
                }
                //
                // if (this.catalogsData.currencies.length) {
                //     this.currencies = this.catalogsData.currencies;
                //     this.currencyId = this.catalogsData.currencies[0].id;
                // }
            }

            this.checkDisabled();
        },

        computed: {
            catalog() {
                return this.catalogs.find(catalog => catalog.id == this.catalogId);
            },
            catalogItemsList() {
                // return this.catalog.items_tree;
                return this.getItemsList(this.catalog.items_tree);
            },
            filialsList() {
                return this.catalog.filials;
            },
            disabledFilial() {


                // var $vm = this,
                //     cur =[this.catalogId, this.catalogItem.id, this.filialId];
                // this.disabledFilials.forEach(disabledFilial => {
                //     if (disabledFilial == cur) {
                //         return true;
                //     }
                // });
                // return false;

                // var cur = [this.catalogId, this.catalogItem.id, this.filialId];
                // return this.disabledFilials.map(price => ({
                //     ...price,
                //     // if the value matched the rule, do not disable it
                //     disabled: cur.indexOf(price) >= 0 ? false : true
                // }))
            },
        },

        methods: {
            getItemsList(items) {
                var tree = [];
                var self = this;

                items.forEach( function(item) {
                    tree.push(item);
                    if (typeof item.childrens !== 'undefined') {
                        tree = tree.concat(self.getItemsList(item.childrens));
                    }
                });

                return tree;
            },
            getCount(level) {
                let res = '';
                for (var i = 1; i < level; i++) {
                    res = res + '_';
                }
                return res;
            },
            checkDisabled() {
                this.error = false;

                if (this.prices.length) {
                    var $vm = this;
                    this.disabledFilials.forEach(disabled => {
                       if (disabled[0] == this.catalogId && disabled[1] == this.catalogItem.id && disabled[2] == this.filialId) {
                           $vm.error = true;

                       }
                    });
                }
            },
            changeCatalog() {
                this.checkDisabled();

                if (this.catalog.items.length) {

                    let found = this.catalog.items.find(item => item.name == this.catalogItem.name);
                    if (found) {
                        this.catalogItem = found;
                    } else {
                        this.catalogItem = this.catalog.items_tree[0];
                    }
                } else {
                    this.catalogsItem = {};
                }

                if (this.catalog.filials.length) {
                    this.filialId = this.catalog.filials[0].id;
                } else {
                    this.filialId = null;
                }
            },
            addPrice() {
                if (this.price && ! this.error) {
                    if (this.price > 0) {
                        var buttons = $('.button');
                        buttons.prop('disabled', true);

                        axios
                            .post('/admin/catalogs_goods/' + this.catalogId + '/prices_goods/ajax_store', {
                                catalogs_goods_item_id: this.catalogItem.id,
                                catalogs_goods_id: this.catalogId,
                                goods_id: this.goodsId,
                                filial_id: this.filialId,
                                currency_id: 1,
                                price: this.price
                            })
                            .then(response => {
                                this.$emit('add', response.data);
                                buttons.prop('disabled', false);
                                this.error = true;
                            })
                            .catch(error => {
                                console.log(error)
                            });
                    }
                }
            },
        }
    }
</script>
