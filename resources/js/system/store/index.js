import moduleEstimate from './modules/estimate'
import modulePromotion from './modules/promotion'

// const debug = process.env.NODE_ENV !== 'production'

let store = {
    modules: {
        estimate: moduleEstimate,
        promotion: modulePromotion,
    },
    // strict: debug,
};

export default store;
