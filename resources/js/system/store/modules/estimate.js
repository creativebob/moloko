const moduleEstimate = {
    state: {
        estimate: null,
        goodsItems: [],
    },
    mutations: {
        GET_ESTIMATE(state, estimate) {
            state.estimate = estimate;
        },
        GET_GOODS_ITEMS(state, goodsItems) {
            state.goodsItems = goodsItems;
        },
        UPDATE_ESTIMATE(state) {
            let amount = 0;
            if (state.goodsItems.length > 0) {
                state.goodsItems.forEach(function(item) {
                    return amount += Number(item.amount)
                });
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
    },
    actions: {
        ADD_GOODS_ITEM_TO_ESTIMATE({ state }, priceId) {
            if (state.estimate.is_saled === 0) {
                axios
                    .post('/admin/estimates_goods_items', {
                        estimate_id: state.estimate.id,
                        price_id: priceId
                    })
                    .then(response => {
                        let item = response.data,
                            index = state.goodsItems.findIndex(obj => obj.id === item.id);

                        if (index > -1) {
                            Vue.set(state.goodsItems, index, item);
                        } else {
                            state.goodsItems.push(response.data);
                        }

                        this.commit('UPDATE_ESTIMATE');
                    })
                    .catch(error => {
                        console.log(error)
                    });
            }
        },
        REMOVE_GOODS_ITEM_FROM_ESTIMATE({ state }, itemId) {
            if (state.estimate.is_saled === 0) {
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
    },
    getters: {
        estimateAmount: state => {
            return state.estimate.amount;
        },
        estimateTotal: state => {
            return state.estimate.total
        }
    }
};

export default moduleEstimate;