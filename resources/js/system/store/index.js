import moduleLead from './modules/lead'
import modulePromotion from './modules/promotion'
import moduleGoods from './modules/goods'

// const debug = process.env.NODE_ENV !== 'production'

let store = {
    modules: {
        lead: moduleLead,
        promotion: modulePromotion,
        goods: moduleGoods,
    },
    // strict: debug,
};

export default store;
