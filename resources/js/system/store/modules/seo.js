const moduleSeo = {
    state: {
        seo: null,
        additionals: [],

        updatingSeo: {
            title: null,
            h1: null,
            description: null,
            keywords: null,
        },
        updatingSeoIndex: null,

        deletingSeo: {
            title: null,
        },
        deletingSeoIndex: null,

        disabledButton: false,
    },
    mutations: {
        SET_SEOS(state, seo) {
            state.seo = seo;

            if (seo.childs && seo.childs.length) {
                state.additionals = seo.childs;
            }
        },
        ADD_SEO(state, seo) {
            state.additionals.push(seo);
        },

        SET_UPDATING_SEO(state, index) {
            state.updatingSeo = state.additionals[index];
            state.updatingSeoIndex = index;
        },
        UPDATE_SEO(state, seo) {
            Vue.set(state.additionals, state.updatingSeoIndex, seo);
        },

        SET_DELETING_SEO(state, index) {
            state.deletingSeo = state.additionals[index];
            state.deletingSeoIndex = index;
        },
        DELETE_SEO(state) {
            state.additionals.splice(state.deletingSeoIndex, 1);
        },

        CHECK_PARAMS(state, data) {
            if (state.additionals.length > 0) {
                const length = data.params.length;
                if (length) {
                    let coincidence = 0;
                    state.additionals.forEach(seo => {
                        if (seo.params.length == length) {
                            data.params.forEach(dataParam => {
                                seo.params.forEach(seoParam => {
                                    if (dataParam.param == seoParam.param && dataParam.value == seoParam.value) {
                                        coincidence++;
                                    }
                                });
                            });
                        }
                    });
                    state.disabledButton = length == coincidence;
                } else {
                    state.disabledButton = false;
                }
            }
        }
    },
};

export default moduleSeo;
