<template>
    <tr>
        <td>{{ index + 1 }}</td>
        <td>{{ item.entity.name }}</td>
        <td>{{ item.cmv.article.name }}</td>

        <td @click="changeCount = true">
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
            <template v-else="changeCount">{{ item.count }}</template>


        </td>
        <td>{{ item.cmv.article.unit.abbreviation }}</td>

        <td @click="changePrice = true">
            <template v-if="isChangePrice">
                <input
                    @keydown.enter.prevent="updateItem"
                    type="number"
                    v-focus
                    @focusout="changePrice = false"
                    v-model="price"
                >
            </template>
            <template v-else="changePrice">{{ item.price }}</template>
        </td>

        <td>{{ item.amount }}</td>
        <!--			<td>{{ item.vat_rate }}</td>-->
        <!--			<td>{{ item.amount_vat }}</td>-->
        <!--			<td>{{ item.total }}</td>-->
        <td>
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
        },
        data() {
            return {
                count: this.item.count,
                price: this.item.price,
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
            updateItem: function() {
                this.changeCount = false;
                this.changePrice = false;
                axios
                    .patch('/admin/consignments_items/' + this.item.id, {
                        count: this.count,
                        price: this.price
                    })
                    .then(response => {
                            this.$parent.updateItems(response.data, this.index);
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
                            this.$parent.deleteItems(this.index);
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
        }
    }
</script>
