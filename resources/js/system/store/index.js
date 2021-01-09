import moduleLead from './modules/leads/leads'
import modulePromotion from './modules/promotion'
import moduleGoods from './modules/goods'
import moduleServices from './modules/services'

// const debug = process.env.NODE_ENV !== 'production'

let store = {
    modules: {
        lead: moduleLead,
        promotion: modulePromotion,
        goods: moduleGoods,
        services: moduleServices,
    },
    // strict: debug,
};

export default store;
