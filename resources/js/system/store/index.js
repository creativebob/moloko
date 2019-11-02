import moduleEstimate from './modules/estimate'

const debug = process.env.NODE_ENV !== 'production'

let store = {
    modules: {
        estimate: moduleEstimate,
    },
    // strict: debug,
};

export default store;