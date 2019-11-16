<template>
    <tr
            class="item"
            :id="'estimates_goods_items-' + item.id"
            :data-name="item.product.article.name"
            :data-price_id="item.price_id"
            :data-count="item.count"
            :data-price="item.price">
<!--        <td>{{ index + 1 }}</td>-->
        <td>{{ item.product.article.name }}</td>
        <td>Сюда цену</td>

<!--        <td>{{ item.count }}</td>-->
        <td @click="checkChangeCount">
            <template v-if="isChangeCount">
                <input
                        @keydown.enter.prevent="updateItem"
                        type="number"
                        v-focus
                        @focusout="changeCount = false"
                        v-model="count"
                >
            </template>
            <template v-else="changeCount">{{ item.count | roundToTwo | level }}</template>
        </td>

        <td class="td-amount"><a class="button green-button" data-open="price-set">{{ item.amount | roundToTwo | level }}</a></td>
        <td class="td-delete">
            <div
                    v-if="!this.isSaled"
                    @click="openModalRemoveItem"
                    class="icon-delete sprite"
                    data-open="delete-estimates_item"
            ></div>
        </td>
        <td class="td-action">
            <div class="wrap-reserved-info active">
                <span class="button-to-reserve" title="Позицию в резерв!"></span>
                <span class="reserved-count">4</span>
            </div>
        </td>
    </tr>
</template>

<script>
    export default {
        name: 'estimates-item-component',
        props: {
            item: Object,
            index: Number,
            isSaled: Boolean,
        },
        data() {
            return {
                countInput: Number(this.item.count),
                cost: Number(this.item.cost),
                changeCount: false,
                changeCost: false,
            }
        },
        computed: {
            isChangeCount() {
                return this.changeCount
            },
            count: {
                get () {
                    return Number(this.item.count);
                },
                set (value) {
                    this.countInput = Number(value)
                }

            },
        //     isChangeCost() {
        //         if (this.changeCost) {
        //             this.changeCount = false
        //         }
        //         return this.changeCost
        //     },
        //     unitAbbreviation() {
        //         let abbr;
        //         if (this.item.cmv.article.package_status === 1) {
        //             abbr = this.item.cmv.article.package_abbreviation;
        //         } else {
        //             abbr = this.item.cmv.article.unit.abbreviation;
        //         }
        //         return abbr;
        //     }
        //
        },
        methods: {
            openModalRemoveItem() {
                this.$emit('open-modal-remove', this.item);
            },
            checkChangeCount() {
                if (this.item.product.serial === 0) {
                    if (!this.isSaled) {
                        this.changeCount = !this.changeCount
                    }
                }
            },
            // checkChangeCost() {
            //     if (!this.isSaled) {
            //         this.changeCost = !this.changeCost
            //     }
            // },
            updateItem: function() {
                this.changeCount = false;
                // this.changeCost = false;
                axios
                    .patch('/admin/estimates_goods_items/' + this.item.id, {
                        count: Number(this.countInput),
                        // cost: Number(this.cost)
                    })
                    .then(response => {
                        this.$emit('update', response.data);
                        this.countInput = Number(response.data.count);
                        // this.cost = Number(response.data.cost);
                    })
                    .catch(error => {
                        console.log(error)
                    });
            },
            // deleteItem: function() {
            //     axios
            //         .delete('/admin/consignments_items/' + this.item.id)
            //         .then(response => {
            //             if(response.data > 0) {
            //                 this.$emit('remove');
            //             }
            //         })
            //         .catch(error => {
            //             console.log(error)
            //         });
            // },
        },
        directives: {
            focus: {
                inserted: function (el) {
                    el.focus()
                }
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
