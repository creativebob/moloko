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

        <td
            v-if="!this.isProduced"
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
            isProduced: Boolean,
        },
        data() {
            return {
                count: Number(this.item.count),
                changeCount: false,
            }
        },
        computed: {
            isChangeCount() {
                return this.changeCount
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
                if (!this.isProduced) {
                    this.changeCount = !this.changeCount
                }
            },
            updateItem: function() {
                this.changeCount = false;
                axios
                    .patch('/admin/productions_items/' + this.item.id, {
                        count: Number(this.count),
                    })
                    .then(response => {
                        this.$emit('update', response.data, this.index);
                        this.count = Number(response.data.count);
                    })
                    .catch(error => {
                        console.log(error)
                    });
            },
            deleteItem: function() {
                axios
                    .delete('/admin/productions_items/' + this.item.id)
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
