<template>
    <tr
        :class="[{'cmv-archive' : isArchive}]"
    >
        <td>{{ index + 1 }}</td>
        <td>{{ item.entity.name }}</td>
        <td>{{ item.cmv.article.name }}<span v-if="isArchive"> (Архивный)</span></td>
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

        <td>
            <template
                v-if="isConducted"
            >
                <div
                    v-if="itemsCount > 1"
                    @click="openModalCancel"
                    class="icon-delete sprite"
                    data-open="modal-cancel"
                ></div>
            </template>

            <a
                v-else
                class="icon-delete sprite"
                @click="deleteItem"
            ></a>
        </td>
    </tr>
</template>

<script>
    export default {
        name: 'productions-item-component',
        props: {
            item: Object,
            index: Number,
            isConducted: String,
            itemsCount: Number,
        },
        data() {
            return {
                count: Number(this.item.count),
                changeCount: false,
            }
        },
        computed: {
            isArchive() {
                return this.item.cmv.archive == 1;
            },
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
                if (!this.isConducted) {
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
                        if(response.data > 0) {
                            // console.log('Удаляем - ' + this.item.id);
                            this.$emit('remove');
                        }
                    })
                    .catch(error => {
                        console.log(error)
                    });
            },
            openModalCancel() {
                this.$emit('open-modal-cancel', this.item);
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
