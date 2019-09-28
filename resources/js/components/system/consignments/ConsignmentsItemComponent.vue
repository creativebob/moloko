<template>
    <tr>
        <td>{{ index + 1 }}</td>
        <td>{{ item.id }}</td>
        <td>{{ item.cmv.article.name }}</td>

        <td @click="changeCount = true">
            <template v-if="changeCount">
                <input
                        @keydown.enter.prevent="updateItem"
                        type="number"
                        autofocus
                        @focusout="changeCount = false"
                        v-model="count"
                >
<!--                <input-digit-component name="count" rate="2" :value="item.count" v-on:countchanged="changeCount"></input-digit-component>-->
            </template>
            <template v-else="changeCount">{{ item.count }}</template>
        </td>

        <td @click="changePrice = true">
            <template v-if="changePrice">
                <input
                        @keydown.enter.prevent="updateItem"
                        type="number"
                        autofocus
                        @focusout="changePrice = false"
                        v-model="price"
                >
            </template>
            <template v-else="changePrice">{{ item.price }}</template>
        </td>

        <td>{{ item.total }}</td>
        <!--			<td>{{ item.vat_rate }}</td>-->
        <!--			<td>{{ item.amount_vat }}</td>-->
        <!--			<td>{{ item.total }}</td>-->
        <td>
            <a
                @click="deleteItem"
                :disabled="disabledButton"
                class="button tiny"
            >Удалить</a>
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
                disabledButton: false,
            }
        },
        computed: {
            isChangeCount() {
                return this.changeCount = !this.changeCount
            },
            isChangePrice() {
                return this.changePrice = !this.changePrice
            },
        },
        methods: {
            updateItem: function() {
                axios
                    .patch('/admin/consignments_items/' + this.item.id, {
                        count: this.count,
                        price: this.price
                    })
                    .then(response => {
                            this.$parent.updItem(response.data, this.index);
                            this.changeCount = false
                            this.changePrice = false
                    })
                    .catch(error => {
                        console.log(error)
                    });
            },
            deleteItem: function() {
                this.disabledButton = true;
                axios
                    .delete('/admin/consignments_items/' + this.item.id)
                    .then(response => {
                        if(response.data == true) {
                            this.$parent.delItem(this.index);
                        }
                    })
                    .catch(error => {
                        console.log(error)
                    });
            },
        }
    }
</script>
