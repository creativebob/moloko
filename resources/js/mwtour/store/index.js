let goodsItems = window.localStorage.getItem('goodsItems');

let store = {
    state: {
        goodsItems: goodsItems ? JSON.parse(goodsItems) : [],
    },
    mutations: {
        ADD_TO_CART(state, item) {
            if (item.count > 0) {
                const index = state.goodsItems.findIndex(obj => obj.id === item.id);
                if (index > -1) {
                    let goodsItem = state.goodsItems[index];

                    let quantity = goodsItem.quantity + item.count;
                    if (goodsItem.is_check_stock == 1) {
                        if (goodsItem.rest) {
                            if (quantity > parseInt(goodsItem.rest)) {
                                quantity = parseInt(goodsItem.rest);
                            }
                        } else {
                            quantity = 0;
                        }
                    }
                    goodsItem.quantity = quantity;

                    goodsItem.total_catalogs_item_discount = parseFloat(goodsItem.total_catalogs_item_discount_unit) * quantity;
                    goodsItem.total = parseFloat(goodsItem.total_unit) * quantity;

                    Vue.set(state.goodsItems, index, goodsItem);
                } else {
                    let quantity = item.count;
                    if (item.is_check_stock == 1) {
                        if (item.rest) {
                            if (quantity > parseInt(item.rest)) {
                                quantity = parseInt(item.rest);
                            }
                        } else {
                            quantity = 0;
                        }
                    } else {
                        quantity = item.count;
                    }

                    if (quantity > 0) {
                        item.quantity = item.count;
                        item.total_catalogs_item_discount = parseFloat(item.total_catalogs_item_discount_unit) * item.count;
                        item.total = parseFloat(item.total_unit) * item.count;

                        state.goodsItems.push(item);
                    }
                }

                this.commit('SAVE_CART');
            }
        },
        DEDUCT_TO_CART(state, item) {
            if (item.count > 0) {
                let found = state.goodsItems.find(product => product.id == item.id);

                if (found) {
                    if (found.quantity > 0) {
                        found.quantity -= item.count;
                        found.total_catalogs_item_discount = found.quantity * parseFloat(found.total_catalogs_item_discount_unit);
                        found.total = found.quantity * parseFloat(found.total_unit);

                        // if (found.quantity == 0) {
                        //     this.commit('REMOVE_FROM_CART', found);
                        // }
                        const index = state.goodsItems.findIndex(obj => obj.id === found.id);
                        Vue.set(state.goodsItems, index, found);
                    }
                }
                this.commit('SAVE_CART');
                // return found.quantity;
            }
        },

        CHANGE_CART(state, data) {
            if (data.count !== '') {
                const index = state.goodsItems.findIndex(obj => obj.id === data.id);
                if (index > -1) {
                    let goodsItem = state.goodsItems[index];

                    let quantity = data.count;
                    if (goodsItem.is_check_stock == 1) {
                        if (goodsItem.rest) {
                            if (quantity > parseInt(goodsItem.rest)) {
                                quantity = parseInt(goodsItem.rest);
                            }
                        } else {
                            quantity = 0;
                        }
                    }
                    goodsItem.quantity = quantity;

                    goodsItem.total_catalogs_item_discount = parseFloat(goodsItem.total_catalogs_item_discount_unit) * quantity;
                    goodsItem.total = parseFloat(goodsItem.total_unit) * quantity;

                    Vue.set(state.goodsItems, index, goodsItem);
                }

                this.commit('SAVE_CART');
            }

        },
        REMOVE_FROM_CART(state, index) {
            // let index = state.goodsItems.findIndex(obj => obj.id === id);
            state.goodsItems.splice(index, 1);

            // console.log(index);
            this.commit('SAVE_CART');
        },

        /**
         * Цена прайса отличается от добаленной в корзину
         *
         * @param state
         * @param items
         * @constructor
         */
        CHANGE_ITEMS_PRICE(state, items) {
            items.forEach(item => {
                const index = state.goodsItems.findIndex(obj => obj.id === item.id);
                if (index > -1) {
                    let goodsItem = state.goodsItems[index];

                    let price = {
                        id: item.id,
                        price: parseFloat(item.price),
                        total_catalogs_item_discount_unit: parseFloat(item.total_catalogs_item_discount),
                        total_catalogs_item_discount: parseFloat(item.total_catalogs_item_discount) * goodsItem.quantity,
                        total_unit: parseFloat(item.total),
                        total: parseFloat(item.total) * goodsItem.quantity,
                        count: goodsItem.quantity,
                        goods: item.goods,
                        currency: item.currency,

                        quantity: goodsItem.quantity,

                        rest: parseInt(item.goods.rest),
                        is_check_stock: item.catalog.is_check_stock,
                    };

                    if (goodsItem.hasOwnProperty('oldRest')) {
                        price.oldRest = goodsItem.rest;
                    }

                    price.oldPrice = goodsItem.total_catalogs_item_discount_unit;

                    Vue.set(state.goodsItems, index, price);
                }
            });
            this.commit('SAVE_CART');
        },

        /**
         * Товара не хватает на складе
         *
         * @param state
         * @param items
         * @constructor
         */
        NOT_ENOUGH_ITEMS(state, items) {
            items.forEach(item => {
                let found = state.goodsItems.find(product => product.id == item.id);

                const index = state.goodsItems.findIndex(obj => obj.id === item.id);
                if (index > -1) {
                    let goodsItem = state.goodsItems[index];

                    let price = {
                        id: item.id,
                        price: parseFloat(item.price),
                        total_catalogs_item_discount_unit: parseFloat(item.total_catalogs_item_discount),
                        total_catalogs_item_discount: parseFloat(item.total_catalogs_item_discount) * goodsItem.quantity,
                        total_unit: parseFloat(item.total),
                        total: parseFloat(item.total) * goodsItem.quantity,
                        count: goodsItem.quantity,
                        goods: item.goods,
                        currency: item.currency,

                        quantity: goodsItem.quantity,

                        rest: parseInt(item.goods.rest),
                        is_check_stock: item.catalog.is_check_stock,
                    };

                    if (goodsItem.hasOwnProperty('oldPrice')) {
                        price.oldPrice = goodsItem.price;
                    }

                    price.oldRest = goodsItem.rest;

                    Vue.set(state.goodsItems, index, price);
                }
            });
            this.commit('SAVE_CART');
        },

        SAVE_CART(state) {
            state.goodsItems.forEach(item => {
                if (parseFloat(item.price) == parseFloat(item.oldPrice)) {
                    delete item.oldPrice;
                }

                if (parseFloat(item.quantity) <= parseFloat(item.rest)) {
                    delete item.oldRest;
                }
            });

            window.localStorage.setItem('goodsItems', JSON.stringify(state.goodsItems));
            // this.dispatch('UPDATE_COOKIES');
        },

        REMOVE_CART(state) {
            state.goodsItems = [];
            window.localStorage.removeItem('goodsItems');
            // this.dispatch('UPDATE_COOKIES');
        },
    },
    actions: {
        UPDATE_COOKIES({state}) {
            // clearTimeout(myTimeout);
            // myTimeout = setTimeout(() => {
            return axios
                .post('/update_cookies', {
                    goodsItems: state.goodsItems,
                })
                .then(response => {
                    // console.log(response.data);
                    return response.data.success;
                })
                .catch(error => {
                    console.log(error)
                });
            // }, 600)
        }
    },
    getters: {
        GOODS_ITEMS: state => {
            return state.goodsItems;
        },
        CART_TOTAL_CATALOGS_ITEM_DISCOUNT: state => {
            let total = 0;
            state.goodsItems.forEach(item => {
                return total += parseFloat(item.total_catalogs_item_discount);
            });
            return total.toFixed(2);
        },
        CART_TOTAL: state => {
            let total = 0;
            state.goodsItems.forEach(item => {
                return total += parseFloat(item.total);
            });
            return total.toFixed(2);
        },
        CART_COUNT: state => {
            let count = 0;
            state.goodsItems.forEach(item => {
                if (item.quantity > 0) {
                    count += parseFloat(item.quantity);
                }
            });
            return count;
        },
        COUNT_IN_CART: state => id => {
            let count = 0,
                found = state.goodsItems.find(item => item.id == id);

            if (found) {
                count = found.quantity;
            }
            return count;
        },
    }
};

export default store;
