<template>
    <tr
        class="item commentable"
        :id="'estimates_goods_items-' + item.id"
        :data-name="item.product.article.name"
        :data-price_id="item.price_id"
        :data-count="item.count"
        :data-price="item.price">
        <!--        <td>{{ index + 1 }}</td>-->
        <td>
            {{ item.product.article.name }}
            <span
                class="icon-comment"
                :class="[{ 'hide-comment' : ! showComment }]"
                :data-toggle="'comment-dropdown-' + item.id"
            ></span>
            <div class="dropdown-pane" :id="'comment-dropdown-' + item.id" data-dropdown data-auto-focus="true">
                <textarea
                    name="comment"
                    @keydown.enter.prevent="updateItemComment"
                    v-model="comment"
                >{{ item.comment }}</textarea>
                <a
                class="button"
                @click="updateItemComment"
                >Сохранить</a>
            </div>
        </td>

<!--        <td v-if="settings.length && stocks.length">-->
<!--            <select-->
<!--                name="stock_id"-->
<!--                v-model="stockId"-->
<!--            >-->
<!--                <option v-for="stock in stocks"-->
<!--                    :value="stock.id"-->
<!--                >{{ stock.name }}</option>-->
<!--            </select>-->
<!--        </td>-->
<!--        <td v-else>-->
<!--            {{ item.stock.name }}-->
<!--        </td>-->

        <td>
            {{ item.price | roundToTwo | level }} 
            <span class="point-value" v-if="item.price_goods.point > 0">({{ item.price_goods.point }})</span>
        </td>

        <!--        <td>{{ item.count }}</td>-->
        <td @click="checkChangeCount">
            <template v-if="isChangeCount">
                <input
                    @keydown.enter.prevent="updateItemCount"
                    type="number"
                    v-focus
                    @focusout="changeCount = false"
                    v-model="count"
                >
            </template>
            <template v-else="changeCount">{{ item.count | roundToTwo | level }}</template>
        </td>

        <td class="td-discount">
            <template
                v-if="item.discount_percent > 0"
            >
                {{ item.discount_percent | roundToTwo | level }} 
                <span class="percent-symbol">%</span>
            </template>
        </td>

        <td class="td-amount">
            <a
                class="button green-button"
                :data-open="'modal-estimates_goods_item-' + item.id"
            >{{ item.total | roundToTwo | level }}</a>
        </td>
        <td class="td-delete">
            <div
                v-if="!isRegistered"
                @click="openModalRemoveItem"
                class="icon-delete sprite"
                data-open="delete-estimates_goods_item"
            ></div>
        </td>
        <td
            v-if="settings.length && isRegistered"
            class="td-action"
        >
            <div
                :class="isReservedClass"
            >
                <span
                    v-if="!isReserved"
                    @click="reserveEstimateItem"
                    class="button-to-reserve"
                    title="Позицию в резерв!"
                ></span>
                <span
                    v-else
                    @click="unreserveEstimateItem"
                    class="button-to-reserve unreserve"
                    title="Снять с резерва!"
                ></span>
                <span
                    v-if="reservedCount > 0"
                    class="reserved-count"
                >{{ reservedCount | roundToTwo | level }}</span>
            </div>
        </td>

        <div
            class="reveal"
            :id="'modal-estimates_goods_item-' + item.id"
            data-reveal
            data-close-on-click="false"
        >
            <div class="grid-x">
                <div class="small-12 cell modal-title">
                    <h5>Настройка позиции</h5>
                </div>
            </div>

            <div class="grid-x grid-padding-x align-center modal-content inputs">
                <div class="small-12 cell">

                    <div class="grid-x grid-margin-x">

                        <div class="small-12 cell">
                            <label>Закупочная цена единицы, руб
                                <input-digit-component
                                    name="cost"
                                    :value="item.cost"
                                    :decimal_place="2"
                                ></input-digit-component>
                            </label>
                        </div>

                        <div class="small-12 medium-12 cell">
                            <fieldset>
                                <legend>Наценка:</legend>
                                <div class="grid-x grid-margin-x">
                                    <div class="small-12 medium-6 cell">
                                        <label>Наценка, %
                                            <input
                                                type="number"
                                                name="margin_percent"
                                                disabled
                                                v-model="markupPercent"
                                            >
<!--                                            <input-digit-component-->
<!--                                                name=""-->
<!--                                                :value="markupPercent"-->
<!--                                                :decimal_place="4"-->
<!--                                                :disabled="true"-->
<!--                                            ></input-digit-component>-->
                                        </label>
                                    </div>
                                    <div class="small-12 medium-6 cell">
                                        <label>Наценка, руб
                                            <input
                                                type="number"
                                                name="margin_currency"
                                                disabled
                                                v-model="markupCurrency"
                                            >
<!--                                            <input-digit-component-->
<!--                                                name="margin_currency"-->
<!--                                                :value="markupCurrency"-->
<!--                                                :decimal_place="2"-->
<!--                                                :disabled="true"-->
<!--                                            ></input-digit-component>-->
                                        </label>
                                    </div>

                                    <hr>

<!--                                    <div class="small-12 medium-6 cell">-->
<!--                                        <label>Допфикс наценка, %-->
<!--                                            <input-digit-component-->
<!--                                                name="extra_margin_percent"-->
<!--                                                :value="item.extra_margin_percent"-->
<!--                                                :decimal_place="4"-->
<!--                                            ></input-digit-component>-->
<!--                                        </label>-->
<!--                                    </div>-->
<!--                                    <div class="small-12 medium-6 cell">-->
<!--                                        <label>Допфикс наценка, руб-->
<!--                                            <input-digit-component-->
<!--                                                name="extra_margin_currency"-->
<!--                                                :value="item.extra_margin_currency"-->
<!--                                                :decimal_place="2"-->
<!--                                            ></input-digit-component>-->
<!--                                        </label>-->
<!--                                    </div>-->
                                </div>
                            </fieldset>
                        </div>


                        <div class="small-12 medium-12 cell">
                            <fieldset>
                                <legend>Скидка:</legend>
                                <div class="grid-x grid-margin-x">
                                    <div class="small-12 medium-6 cell">
                                        <label>Скидка, %
                                            <input
                                                type="number"
                                                name="discount_percent"
                                                v-model="discountPercent"
                                                @input="changeDiscountPercent"
                                            >
<!--                                            <input-digit-component-->
<!--                                                name="discount_percent"-->
<!--                                                :value="item.discount_percent"-->
<!--                                                :decimal_place="4"-->
<!--                                            ></input-digit-component>-->
                                        </label>
                                    </div>
                                    <div class="small-12 medium-6 cell">
                                        <label>Скидка, руб
                                            <input
                                                type="number"
                                                name="discount_currency"
                                                v-model="discountCurrency"
                                                @input="changeDiscountCurrency"
                                            >
<!--                                            <input-digit-component-->
<!--                                                name="discount_currency"-->
<!--                                                :value="item.discount_currency"-->
<!--                                                :decimal_place="2"-->
<!--                                            ></input-digit-component>-->
                                        </label>
                                    </div>

<!--                                    <div class="small-12 medium-6 cell">-->
<!--                                        <label>Допфикс скидка, %-->
<!--                                            <input-digit-component-->
<!--                                                name="extra_discount_percent"-->
<!--                                                :value="item.extra_discount_percent"-->
<!--                                                :decimal_place="4"-->
<!--                                            ></input-digit-component>-->
<!--                                        </label>-->
<!--                                    </div>-->
<!--                                    <div class="small-12 medium-6 cell">-->
<!--                                        <label>Допфикс скидка, руб-->
<!--                                            <input-digit-component-->
<!--                                                name="extra_discount_currency"-->
<!--                                                :value="item.extra_discount_currency"-->
<!--                                                :decimal_place="2"-->
<!--                                            ></input-digit-component>-->
<!--                                        </label>-->
<!--                                    </div>-->
                                </div>
                            </fieldset>
                        </div>


                        <div class="small-12 medium-6 cell">
                            <label>Цена единицы, руб
                                <input-digit-component
                                    name="price"
                                    :value="item.price"
                                    :decimal_place="2"
                                    :disabled="true"
                                ></input-digit-component>
                            </label>
                        </div>
                        <div class="small-12 medium-6 cell">
                            <label>Количество, единиц
                                <input
                                    type="number"
                                    name="count"
                                    v-model="count"
                                >
<!--                                <input-digit-component-->
<!--                                    name="count"-->
<!--                                    :value="item.count"-->
<!--                                ></input-digit-component>-->
                            </label>
                        </div>

                        <div class="small-12 cell">
                            <label>Итоговая стоимость по позиции, руб
                                <input
                                    type="number"
                                    name="total"
                                    v-model="total"
                                >
<!--                                <input-digit-component-->
<!--                                    name="total"-->
<!--                                    :value="item.total"-->
<!--                                    :decimal_place="2"-->
<!--                                    :disabled="true"-->
<!--                                ></input-digit-component>-->
                            </label>
                        </div>

                    </div>

<!--                    <div class="grid-x grid-margin-x">-->
<!--                        <div class="small-12 cell">-->
<!--                            Склад:-->
<!--                        </div>-->
<!--                    </div>-->

                </div>
            </div>
            <div
                v-if="estimate.is_registered != 1 && estimate.is_saled != 1"
                class="grid-x align-center"
            >
                <div class="small-6 medium-4 cell">
                    <button
                        @click="updateItem"
                        class="button modal-button"
                    >Сохранить</button>
                </div>
            </div>
            <div data-close class="icon-close-modal sprite close-modal add-item"></div>
        </div>
    </tr>
</template>

<script>
    export default {
        components: {
            'input-digit-component': require('../../../../common/components/inputs/DigitComponent.vue')
        },
        props: {
            item: Object,
            index: Number,
            isRegistered: Boolean,
            settings: Array,
            stocks: Array,
        },
        data() {
            return {
                countInput: Number(this.item.count),
                cost: Number(this.item.cost),
                changeCount: false,
                changeCost: false,
                stockId: null,
                markupPercent: 0,
                markupCurrency: 0,
                discountPercent: this.item.discount_percent,
                discountCurrency: this.item.discount_currency,
                comment: this.item.comment,
                count: this.item.count,
            }
        },
        mounted() {
            if(this.settings.length && this.stocks.length && this.item.stock_id === null) {
                this.stockId = this.stocks[0].id;
            } else {
                this.stockId = this.item.stock_id;
            }

            this.markupCurrency = Number(this.item.price - this.item.cost);
            this.markupPercent = this.markupCurrency / (this.item.cost / 100);
        },
        computed: {
            estimate() {
                return this.$store.state.estimate.estimate;
            },
            isChangeCount() {
                return this.changeCount
            },
            // count: {
            //     get () {
            //         return Number(this.item.count);
            //     },
            //     set (value) {
            //         this.countInput = Number(value)
            //     }
            // },
            isReservedClass() {
                if (this.item.reserve !== null) {
                    if (this.item.reserve.count > 0) {
                        return 'wrap-reserved-info active';
                    }
                }
                return 'wrap-reserved-info';
            },
            isReserved() {
                if (this.item.reserve !== null) {
                    if (this.item.reserve.count > 0) {
                        return true;
                    }
                }
                return false;
            },
            reservedCount() {
                if (this.item.reserve !== null) {
                    if (this.item.reserve.count > 0) {
                        return this.item.reserve.count;
                    }
                }
                return 0;
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
            total() {
                return (this.item.price - this.discountCurrency) * this.count;
            },
            showComment() {
                if (this.item.comment != null) {
                    return this.item.comment.length > 0;
                } else {
                    return false;
                }
            },
        },
        methods: {
            openModalRemoveItem() {
                this.$emit('open-modal-remove', this.item);
            },
            checkChangeCount() {
                if (this.item.product.serial === 0) {
                    if (!this.isRegistered) {
                        this.changeCount = !this.changeCount
                    }
                }
            },
            // checkChangeCost() {
            //     if (!this.isRegistered) {
            //         this.changeCost = !this.changeCost
            //     }
            // },
            updateItemCount: function() {
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
            updateItemComment() {
                axios
                    .patch('/admin/estimates_goods_items/' + this.item.id, {
                        comment: this.comment,
                    })
                    .then(response => {
                        this.$emit('update', response.data);
                        this.comment = response.data.comment;
                    })
                    .catch(error => {
                        console.log(error)
                    });
            },
            updateItem() {
                axios
                    .patch('/admin/estimates_goods_items/' + this.item.id, {
                        count: this.count,
                        discount_currency: this.discountCurrency,
                        discount_percent: this.discountPercent,
                    })
                    .then(response => {
                        this.$emit('update', response.data);
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
            reserveEstimateItem() {
                axios
                    .post('/admin/estimates_goods_items/' + this.item.id + '/reserving')
                    .then(response => {
                        if (response.data.msg !== null) {
                            alert(response.data.msg);
                        }
                        this.$emit('update', response.data.item);
                    })
                    .catch(error => {
                        console.log(error)
                    });
            },
            unreserveEstimateItem() {
                axios
                    .post('/admin/estimates_goods_items/' + this.item.id + '/unreserving')
                    .then(response => {
                        if (response.data.msg !== null) {
                            alert(response.data.msg);
                        }
                        this.$emit('update', response.data.item);
                    })
                    .catch(error => {
                        console.log(error)
                    });
            },
            changeDiscountPercent() {
                let percent = this.item.price / 100;
                this.discountCurrency = this.discountPercent* percent;
            },
            changeDiscountCurrency() {
                let percent = this.item.price / 100;
                this.discountPercent = (this.item.price - this.discountCurrency) / percent;
            }
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
                return parseInt(value).toLocaleString();
            },

            // Отбраcывает дробную часть в строке с числами
            onlyInteger(value) {
                return Math.floor(value);
            },
        },
    }
</script>
