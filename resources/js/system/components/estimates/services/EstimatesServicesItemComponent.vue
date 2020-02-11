<template>
    <tr
        class="item"
        :id="'estimates_services_items-' + item.id"
        :data-name="item.product.process.name"
        :data-price_id="item.price_id"
        :data-count="item.count"
        :data-price="item.price">
        <!--        <td>{{ index + 1 }}</td>-->
        <td>
            {{ item.product.process.name }}
            <span class="icon-comment"></span>
        </td>
        <td>{{ item.price | roundToTwo | level }}</td>

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
                data-open="delete-estimates_services_item"
            ></div>
        </td>
        <td class="td-action">

        </td>
    </tr>
</template>

<script>
    export default {
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
            updateItem: function() {
                this.changeCount = false;
                // this.changeCost = false;
                axios
                    .patch('/admin/estimates_services_items/' + this.item.id, {
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
