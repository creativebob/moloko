<template>
    <tr>
        <td>{{ index + 1 }}</td>
        <td>{{ item.entity.name }}</td>
        <td>{{ item.cmv.article.name }}</td>

        <td @click="checkChangeCount">
            <template v-if="isChangeCount">
                <input
                    @keydown.enter.prevent="updateItem"
                    type="number"
                    v-focus
                    @focusout="changeCount = false"
                    v-model="count"
                >
<!--                <input-digit-component name="count" rate="2" :value="item.count" v-on:countchanged="changeCount"></input-digit-component>-->
            </template>
            <template v-else="changeCount">{{ item.count | roundToTwo | level }}</template>


        </td>
        <td>{{ item.cmv.article.unit.abbreviation }}</td>

        <td @click="checkChangePrice">
            <template v-if="isChangePrice">
                <input
                    @keydown.enter.prevent="updateItem"
                    type="number"
                    v-focus
                    @focusout="changePrice = false"
                    v-model="price"
                >
            </template>
            <template v-else="changePrice">{{ item.price | roundToTwo | level }}</template>
        </td>

        <td>{{ item.amount | roundToTwo | level }}</td>
        <!--			<td>{{ item.vat_rate }}</td>-->
        <!--			<td>{{ item.amount_vat }}</td>-->
        <!--			<td>{{ item.total }}</td>-->
        <td
            v-if="!this.isPosted"
        >
            <a
                class="icon-delete sprite"
                @click="deleteItem"
            ></a>
        </td>
    </tr>
</template>

<script>
    export default {
        name: 'consignments-item-component',
        props: {
            item: Object,
            index: Number,
            isPosted: Boolean,
        },
        data() {
            return {
                count: Number(this.item.count),
                price: Number(this.item.price),
                changeCount: false,
                changePrice: false,
            }
        },
        computed: {
            isChangeCount() {
                if (this.changeCount) {
                    this.changePrice = false
                }
                return this.changeCount
            },
            isChangePrice() {
                if (this.changePrice) {
                    this.changeCount = false
                }
                return this.changePrice
            },

        },
        methods: {
            checkChangeCount() {
                if (!this.isPosted) {
                    this.changeCount = !this.changeCount
                }
            },
            checkChangePrice() {
                if (!this.isPosted) {
                    this.changePrice = !this.changePrice
                }
            },
            updateItem: function() {
                this.changeCount = false;
                this.changePrice = false;
                axios
                    .patch('/admin/consignments_items/' + this.item.id, {
                        count: Number(this.count),
                        price: Number(this.price)
                    })
                    .then(response => {
                        this.$emit('update', response.data, this.index);
                        this.price = Number(response.data.price);
                        this.count = Number(response.data.count);
                    })
                    .catch(error => {
                        console.log(error)
                    });
            },
            deleteItem: function() {
                axios
                    .delete('/admin/consignments_items/' + this.item.id)
                    .then(response => {
                        if(response.data === true) {
                            this.$emit('remove');
                        }
                    })
                    .catch(error => {
                        console.log(error)
                    });
            },
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
