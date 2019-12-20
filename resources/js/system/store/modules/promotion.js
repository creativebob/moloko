const modulePromotion = {
    state: {
        site: {},
        prices: []
    },
    mutations: {
        SET_SITE(state, site) {
            state.site = site;
        },
        ADD_PRICE(state, price) {
            state.prices.push(price);
        },
        REMOVE_PRICE(state, priceId) {
            let index = state.prices.findIndex(obj => obj.id === priceId);
            state.prices.splice(index, 1);
        }
        // ADD_SITE(state, site) {
        //     state.sites.push(site);
        // },
        // REMOVE_SITE(state, siteId) {
        //     let index = state.sites.findIndex(obj => obj.id === siteId);
        //     state.sites.splice(index, 1);
        //
        //     let ind = state.prices.findIndex(obj => obj.siteId === siteId);
        //     state.prices.splice(ind, 1);
        // },
    },

};

export default modulePromotion;
