const modulePromotion = {
    state: {
        sites: [],
        siteId: 2,
        prices: []
    },
    mutations: {
        INIT_PROMOTION(state, sites) {
            state.sites = sites;
        },
        ADD_SITE(state, site) {
            state.sites.push(site);
        },
        REMOVE_SITE(state, siteId) {
            let index = state.sites.findIndex(obj => obj.id === siteId);
            state.sites.splice(index, 1);

            let ind = state.prices.findIndex(obj => obj.siteId === siteId);
            state.prices.splice(ind, 1);
        },
        ADD_PRICE(state, item) {
            let found = state.prices.find(obj => obj.siteId == item.siteId);
            if (found) {
                found.prices.push(item.price);
            } else {
                state.prices.push({
                    siteId: item.siteId,
                    site: item.site,
                    prices: [item.price]
                });
            }
        },
        REMOVE_PRICE(state, item) {
            let foundSite = state.prices.find(obj => obj.siteId == item.siteId);
            if (foundSite) {
                let index = foundSite.prices.findIndex(obj => obj.id === item.priceId);
                foundSite.prices.splice(index, 1);

                if (!foundSite.prices.length) {
                    let index = state.prices.findIndex(obj => obj.siteId === foundSite.siteId);
                    state.prices.splice(index, 1);
                }
            }
        }
        // SET_GOODS_ITEMS(state, goodsItems) {
        //     state.goodsItems = goodsItems;
        // },
        // UPDATE_ESTIMATE(state) {
        //     let amount = 0;
        //     if (state.goodsItems.length > 0) {
        //         state.goodsItems.forEach(function(item) {
        //             return amount += Number(item.amount)
        //         });
        //     }
        //     state.estimate.amount = amount;
        //
        //     let total = 0;
        //     if (amount > 0) {
        //         let discountAmount = (amount * state.estimate.discount_percent) / 100;
        //
        //         total += amount - discountAmount;
        //     }
        //     state.estimate.total = total;
        // },

        // UPDATE_GOODS_ITEMS(state, goodsItems) {
        //     state.goodsItems = goodsItems;
        //
        //     this.commit('UPDATE_ESTIMATE');
        // },
        // SET_STOCK_ID(state, stockId) {
        //     state.stockId = stockId;
        // },
    },
    // actions: {
    //     ADD_GOODS_ITEM_TO_ESTIMATE({ state }, priceId) {
    //         if (state.estimate.is_saled === 0) {
    //             axios
    //                 .post('/admin/estimates_goods_items', {
    //                     estimate_id: state.estimate.id,
    //                     price_id: priceId,
    //                     stock_id: state.stockId
    //                 })
    //                 .then(response => {
    //                     if (response.data.success) {
    //                         let item = response.data.item,
    //                             index = state.goodsItems.findIndex(obj => obj.id === item.id);
    //
    //                         if (index > -1) {
    //                             Vue.set(state.goodsItems, index, item);
    //                         } else {
    //                             state.goodsItems.push(item);
    //                         }
    //
    //                         this.commit('UPDATE_ESTIMATE');
    //                     } else {
    //                         alert('Невозможно добавить позицию, так как цена изменилась. Удалите позицию со старой ценой и добавьте позицию заново');
    //                     }
    //
    //                 })
    //                 .catch(error => {
    //                     console.log(error)
    //                 });
    //         }
    //     },
    //     REMOVE_GOODS_ITEM_FROM_ESTIMATE({ state }, itemId) {
    //         if (state.estimate.is_saled === 0) {
    //             axios
    //                 .delete('/admin/estimates_goods_items/' + itemId)
    //                 .then(response => {
    //                     if (response.data > 0) {
    //                         let index = state.goodsItems.findIndex(obj => obj.id === itemId);
    //                         state.goodsItems.splice(index, 1);
    //
    //                         this.commit('UPDATE_ESTIMATE');
    //                     }
    //                 })
    //                 .catch(error => {
    //                     console.log(error)
    //                 });
    //         }
    //     },
    // },
    // getters: {
    //     estimateAmount: state => {
    //         return state.estimate.amount;
    //     },
    //     estimateTotal: state => {
    //         return state.estimate.total
    //     }
    // }
};

export default modulePromotion;
