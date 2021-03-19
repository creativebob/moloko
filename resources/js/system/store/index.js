import moduleLead from './modules/leads/leads'
import modulePromotion from './modules/promotion'
import moduleGoods from './modules/goods'
import moduleServices from './modules/services'
import moduleSeo from './modules/seo'

// const debug = process.env.NODE_ENV !== 'production'

let store = {
    modules: {
        lead: moduleLead,
        promotion: modulePromotion,
        goods: moduleGoods,
        services: moduleServices,
        seo: moduleSeo,
    },
    // strict: debug,
};

export default store;
