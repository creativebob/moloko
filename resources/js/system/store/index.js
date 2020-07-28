import moduleEstimate from './modules/estimate'
import modulePromotion from './modules/promotion'
import moduleGoods from './modules/goods'

// const debug = process.env.NODE_ENV !== 'production'

let store = {
    modules: {
        estimate: moduleEstimate,
        promotion: modulePromotion,
        goods: moduleGoods,
    },
    // strict: debug,
};

export default store;
