const moduleLead = {
    state: {
        estimate: {},
        goodsItems: [],
        servicesItems: [],
        client: null,
    },
    mutations: {
        // Смета
        SET_ESTIMATE(state, estimate) {
            state.estimate = estimate;
        },
        UPDATE_ESTIMATE(state) {
            // var amount = 0;
            // if (state.goodsItems.length > 0) {
            //     var goodsItemsAmount = 0;
            //     state.goodsItems.forEach(function(item) {
            //         return goodsItemsAmount += Number(item.amount)
            //     });
            //     state.goodsItems.amount = goodsItemsAmount;
            //     amount += goodsItemsAmount;
            // }
            // if (state.servicesItems.length > 0) {
            //     var servicesItemsAmount = 0;
            //     state.servicesItems.forEach(function(item) {
            //         return servicesItemsAmount += Number(item.amount)
            //     });
            //     state.servicesItems.amount = servicesItemsAmount
            //     amount += servicesItemsAmount;
            // }
            // state.estimate.amount = amount;
            //
            // let total = 0;
            // if (amount > 0) {
            //     let discountAmount = (amount * state.estimate.discount_percent) / 100;
            //
            //     total += amount - discountAmount;
            // }
            // state.estimate.total = total;
        },

        // Товары
        SET_GOODS_ITEMS(state, goodsItems) {
            state.goodsItems = goodsItems;
        },
        UPDATE_GOODS_ITEM(state, item) {
            let index = state.goodsItems.findIndex(obj => obj.id === item.id);
            Vue.set(state.goodsItems, index, item);

            this.commit('UPDATE_ESTIMATE');
        },
        UPDATE_GOODS_ITEMS(state, goodsItems) {
            state.goodsItems = goodsItems;

            this.commit('UPDATE_ESTIMATE');
        },

        // Услуги
        SET_SERVICES_ITEMS(state, servicesItems) {
            state.servicesItems = servicesItems;
        },
        UPDATE_SERVICES_ITEM(state, item) {
            let index = state.servicesItems.findIndex(obj => obj.id === item.id);
            Vue.set(state.servicesItems, index, item);

            this.commit('UPDATE_ESTIMATE');
        },
        UPDATE_SERVICES_ITEMS(state, servicesItems) {
            state.servicesItems = servicesItems;

            this.commit('UPDATE_ESTIMATE');
        },

        // Скидки

        // SET_DISCOUNTS(state, discounts) {
        //     state.discounts = discounts;
        // },

    },
    actions: {
        // Товары
        ADD_GOODS_ITEM_TO_ESTIMATE({ state }, priceId) {
            if (state.estimate.registered_at) {

                axios
                    .post('/admin/estimates_goods_items', {
                        estimate_id: state.estimate.id,
                        price_id: priceId,
                        client_discount_percent: state.client ? state.client.discount : 0,
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
        REMOVE_GOODS_ITEM_FROM_ESTIMATE({ state }, itemId) {
            if (state.estimate.registered_at) {
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

        // Услуги
        ADD_SERVICES_ITEM_TO_ESTIMATE({ state }, priceId) {
            if (state.estimate.registered_at) {
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
        REMOVE_SERVICES_ITEM_FROM_ESTIMATE({ state }, itemId) {
            if (state.estimate.registered_at) {
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

        // Смета
        ESTIMATE_AMOUNT: state => {
            let goodsAmount = 0,
                servicesAmount = 0;

            if (state.goodsItems.length) {
                state.goodsItems.forEach(item => {
                    return goodsAmount += parseFloat(item.amount)
                });
            }

            if (state.servicesItems.length) {
                state.servicesItems.forEach(item => {
                    return servicesAmount += parseFloat(item.amount)
                });
            }

            let amount = goodsAmount + servicesAmount;
            return amount.toFixed(2);
        },
        ESTIMATE_TOTAL_POINTS: state => {
            let goodsTotalPoints = 0,
                servicesTotalPoints = 0;

            if (state.goodsItems.length) {
                state.goodsItems.forEach(item => {
                    return goodsTotalPoints += parseFloat(item.total_points)
                });
            }

            if (state.servicesItems.length) {
                state.servicesItems.forEach(item => {
                    return servicesTotalPoints += parseFloat(item.total_points)
                });
            }

            let totalPoints = goodsTotalPoints + servicesTotalPoints;
            return totalPoints.toFixed(2);
        },
        ESTIMATE_ITEMS_DISCOUNT: state => {
            let goodsDiscount = 0,
                servicesDiscount = 0;

            if (state.goodsItems.length) {
                state.goodsItems.forEach(item => {
                    return goodsDiscount += parseFloat(item.discount_currency)
                });
            }

            if (state.servicesItems.length) {
                state.servicesItems.forEach(item => {
                    return servicesDiscount += parseFloat(item.discount_currency)
                });
            }

            let discount = goodsDiscount + servicesDiscount;
            return discount.toFixed(2);
        },
        // ESTIMATE_DISCOUNT: state => {
        //     let discount = null;
        //     if (state.discounts && state.discounts.length) {
        //         discount = state.estimate.discounts[0];
        //     }
        //     return discount;
        // },
        // ESTIMATE_DISCOUNTCurrency: state => {
        //     let goodsTotal = 0,
        //         servicesTotal = 0;
        //
        //     if (state.goodsItems.length) {
        //         state.goodsItems.forEach(item => {
        //             return goodsTotal += parseFloat(item.total)
        //         });
        //     }
        //
        //     if (state.servicesItems.length) {
        //         state.servicesItems.forEach(item => {
        //             return servicesTotal += parseFloat(item.total)
        //         });
        //     }
        //
        //     let total = goodsTotal + servicesTotal;
        //
        //     let discount = null,
        //         discountCurrency = 0;
        //     if (state.discounts && state.discounts.length) {
        //         discount = state.estimate.discounts[0];
        //     }
        //     if (discount) {
        //         switch (discount.mode) {
        //             case (1):
        //                 let percent = total / 100;
        //                 discountCurrency = discount.percent * percent;
        //                 break;
        //
        //             case (2):
        //                 discountCurrency = discount.currency;
        //                 break;
        //         }
        //     }
        //
        //     return discountCurrency;
        // },
        ESTIMATE_TOTAL: state => {
            let goodsTotal = 0,
                servicesTotal = 0;

            if (state.goodsItems.length) {
                state.goodsItems.forEach(item => {
                    return goodsTotal += parseFloat(item.total)
                });
            }

            if (state.servicesItems.length) {
                state.servicesItems.forEach(item => {
                    return servicesTotal += parseFloat(item.total)
                });
            }

            let total = goodsTotal + servicesTotal;

            // let discount = null,
            //     discountCurrency = 0;
            // if (state.discounts && state.discounts.length) {
            //     discount = state.estimate.discounts[0];
            // }
            // if (discount) {
            //     switch (discount.mode) {
            //         case (1):
            //             let percent = total / 100;
            //             discountCurrency = discount.percent * percent;
            //             break;
            //
            //         case (2):
            //             discountCurrency = discount.currency;
            //             break;
            //     }
            // }
            //
            // let totalWithDiscount = total - parseFloat(discountCurrency);
            return total.toFixed(2);
        },

        // Клиент
        clientDiscountPercent: state => {
            return state.client ? state.client.discount : 0;
        },

    }
};

export default moduleLead;
