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
        <td>{{ item.count }}</td>
        <td><a class="button green-button" data-open="price-set">{{ item.price | roundToTwo | level }}</a></td>
        <td
            v-if="!this.isSaled"
        >
            <div
                    class="icon-delete sprite"
                    data-open="delete-estimates_item"
            ></div>

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
                count: Number(this.item.count),
                cost: Number(this.item.cost),
                changeCount: false,
                changeCost: false,
            }
        },
        // computed: {
        //     isChangeCount() {
        //         if (this.changeCount) {
        //             this.changeCost = false
        //         }
        //         return this.changeCount
        //     },
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
        // },
        methods: {
            // checkChangeCount() {
            //     if (!this.isSaled) {
            //         this.changeCount = !this.changeCount
            //     }
            // },
            // checkChangeCost() {
            //     if (!this.isSaled) {
            //         this.changeCost = !this.changeCost
            //     }
            // },
            // updateItem: function() {
            //     this.changeCount = false;
            //     this.changeCost = false;
            //     axios
            //         .patch('/admin/consignments_items/' + this.item.id, {
            //             count: Number(this.count),
            //             cost: Number(this.cost)
            //         })
            //         .then(response => {
            //             this.$emit('update', response.data, this.index);
            //             this.cost = Number(response.data.cost);
            //             this.count = Number(response.data.count);
            //         })
            //         .catch(error => {
            //             console.log(error)
            //         });
            // },
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
