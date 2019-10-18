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
        <td>{{ unitAbbreviation }}</td>

        <td @click="checkChangeCost">
            <template v-if="isChangeCost">
                <input
                    @keydown.enter.prevent="updateItem"
                    type="number"
                    v-focus
                    @focusout="changeCost = false"
                    v-model="cost"
                >
            </template>
            <template v-else="changeCost">{{ item.cost | roundToTwo | level }}</template>
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
                cost: Number(this.item.cost),
                changeCount: false,
                changeCost: false,
            }
        },
        computed: {
            isChangeCount() {
                if (this.changeCount) {
                    this.changeCost = false
                }
                return this.changeCount
            },
            isChangeCost() {
                if (this.changeCost) {
                    this.changeCount = false
                }
                return this.changeCost
            },
            unitAbbreviation() {
                let abbr;
                if (this.item.cmv.article.package_status === 1) {
                    abbr = this.item.cmv.article.package_abbreviation;
                } else {
                    abbr = this.item.cmv.article.unit.abbreviation;
                }
                return abbr;
            }

        },
        methods: {
            checkChangeCount() {
                if (!this.isPosted) {
                    this.changeCount = !this.changeCount
                }
            },
            checkChangeCost() {
                if (!this.isPosted) {
                    this.changeCost = !this.changeCost
                }
            },
            updateItem: function() {
                this.changeCount = false;
                this.changeCost = false;
                axios
                    .patch('/admin/consignments_items/' + this.item.id, {
                        count: Number(this.count),
                        cost: Number(this.cost)
                    })
                    .then(response => {
                        this.$emit('update', response.data, this.index);
                        this.cost = Number(response.data.cost);
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
                        if(response.data > 0) {
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
