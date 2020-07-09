const moduleEstimate = {
    state: {
        estimate: null,
        goodsItems: [],
        servicesItems: [],
    },
    mutations: {
        SET_ESTIMATE(state, estimate) {
            state.estimate = estimate;
        },
        SET_GOODS_ITEMS(state, goodsItems) {
            state.goodsItems = goodsItems;
        },
        SET_SERVICES_ITEMS(state, servicesItems) {
            state.servicesItems = servicesItems;
        },
        UPDATE_ESTIMATE(state) {
            var amount = 0;
            if (state.goodsItems.length > 0) {
                var goodsItemsAmount = 0;
                state.goodsItems.forEach(function(item) {
                    return goodsItemsAmount += Number(item.amount)
                });
                state.goodsItems.amount = goodsItemsAmount;
                amount += goodsItemsAmount;
            }
            if (state.servicesItems.length > 0) {
                var servicesItemsAmount = 0;
                state.servicesItems.forEach(function(item) {
                    return servicesItemsAmount += Number(item.amount)
                });
                state.servicesItems.amount = servicesItemsAmount
                amount += servicesItemsAmount;
            }
            state.estimate.amount = amount;

            let total = 0;
            if (amount > 0) {
                let discountAmount = (amount * state.estimate.discount_percent) / 100;

                total += amount - discountAmount;
            }
            state.estimate.total = total;
        },
        UPDATE_GOODS_ITEM(state, item) {
            let index = state.goodsItems.findIndex(obj => obj.id === item.id);
            Vue.set(state.goodsItems, index, item);

            this.commit('UPDATE_ESTIMATE');
        },
        UPDATE_SERVICES_ITEM(state, item) {
            let index = state.servicesItems.findIndex(obj => obj.id === item.id);
            Vue.set(state.servicesItems, index, item);

            this.commit('UPDATE_ESTIMATE');
        },
        UPDATE_GOODS_ITEMS(state, goodsItems) {
            state.goodsItems = goodsItems;

            this.commit('UPDATE_ESTIMATE');
        },
        UPDATE_SERVICES_ITEMS(state, servicesItems) {
            state.servicesItems = servicesItems;

            this.commit('UPDATE_ESTIMATE');
        },
        ADD_PAYMENT(state, payment) {
            state.estimate.payments.push(payment);
        }
    },
    actions: {
        ADD_GOODS_ITEM_TO_ESTIMATE({ state }, priceId) {
            if (state.estimate.is_registered === 0) {
                axios
                    .post('/admin/estimates_goods_items', {
                        estimate_id: state.estimate.id,
                        price_id: priceId,
                    })
                    .then(response => {
                        if (response.data.success) {
                            let item = response.data.item,
                                index = state.goodsItems.findIndex(obj => obj.id === item.id);

                            if (index > -1) {
                                Vue.set(state.goodsItems, index, item);
                            } else {
                                state.goodsItems.push(item);
                            }

                            this.commit('UPDATE_ESTIMATE');
                        } else {
                            alert('Невозможно добавить позицию, так как цена изменилась. Удалите позицию со старой ценой и добавьте позицию заново');
                        }

                    })
                    .catch(error => {
                        console.log(error)
                    });
            }
        },
        ADD_SERVICES_ITEM_TO_ESTIMATE({ state }, priceId) {
            if (state.estimate.is_registered === 0) {
                axios
                    .post('/admin/estimates_services_items', {
                        estimate_id: state.estimate.id,
                        price_id: priceId,
                    })
                    .then(response => {
                        if (response.data.success) {
                            let item = response.data.item,
                                index = state.servicesItems.findIndex(obj => obj.id === item.id);

                            if (index > -1) {
                                Vue.set(state.servicesItems, index, item);
                            } else {
                                state.servicesItems.push(item);
                            }

                            this.commit('UPDATE_ESTIMATE');
                        } else {
                            alert('Невозможно добавить позицию, так как цена изменилась. Удалите позицию со старой ценой и добавьте позицию заново');
                        }

                    })
                    .catch(error => {
                        console.log(error)
                    });
            }
        },
        REMOVE_GOODS_ITEM_FROM_ESTIMATE({ state }, itemId) {
            if (state.estimate.is_registered === 0) {
                axios
                    .delete('/admin/estimates_goods_items/' + itemId)
                    .then(response => {
                        if (response.data > 0) {
                            let index = state.goodsItems.findIndex(obj => obj.id === itemId);
                            state.goodsItems.splice(index, 1);

                            this.commit('UPDATE_ESTIMATE');
                        }
                    })
                    .catch(error => {
                        console.log(error)
                    });
            }
        },
        REMOVE_SERVICES_ITEM_FROM_ESTIMATE({ state }, itemId) {
            if (state.estimate.is_registered === 0) {
                axios
                    .delete('/admin/estimates_services_items/' + itemId)
                    .then(response => {
                        if (response.data > 0) {
                            let index = state.servicesItems.findIndex(obj => obj.id === itemId);
                            state.servicesItems.splice(index, 1);

                            this.commit('UPDATE_ESTIMATE');
                        }
                    })
                    .catch(error => {
                        console.log(error)
                    });
            }
        },
    },
    getters: {
        goodsItemsAmount: state => {
            let amount = 0;
            if (state.goodsItems.length) {
                state.goodsItems.forEach(function(item) {
                    return amount += Number(item.amount)
                });
            }
            return amount;
        },
        goodsItemsTotal: state => {
            let total = 0;
            if (state.goodsItems.length) {
                state.goodsItems.forEach(function(item) {
                    return total += Number(item.total)
                });
            }
            return total;
        },
        goodsItemsDiscount: state => {
            let discount = 0;
            if (state.goodsItems.length) {
                state.goodsItems.forEach(function(item) {
                    return discount += Number(item.discount_currency)
                });
            }
            return discount;
        },
        servicesItemsAmount: state => {
            let amount = 0;
            if (state.servicesItems.length) {
                state.servicesItems.forEach(function(item) {
                    return amount += Number(item.amount)
                });
            }
            return amount;
        },
        servicesItemsTotal: state => {
            let total = 0;
            let servicesItemsAmount = state.servicesItemsAmount;
            if (servicesItemsAmount > 0) {
                let discountAmount = (servicesItemsAmount * state.estimate.discount_percent) / 100;
                total = servicesItemsAmount - discountAmount;
            }
            return total;
        },
        estimateAmount: state => {
            return parseInt(state.estimate.amount);
        },
        estimateTotal: state => {
            return parseInt(state.estimate.total);
        },
        paymentsAmount: state => {
            let amount = 0;
            if (state.estimate.payments.length) {
                state.estimate.payments.forEach(function(item) {
                    return amount += Number(item.amount)
                });
            }
            return amount;
        }
    }
};

export default moduleEstimate;
