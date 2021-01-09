const moduleGoods = {
    state: {
        compositions: [],
        totalLength: 0,
        totalCost: 0
    },
    mutations: {
        SET_COMPOSITION(state, composition) {
            if (state.compositions.length) {
                const index = state.compositions.findIndex(obj => obj.name == composition.name);
                if (state.compositions[index]) {
                    state.compositions[index] = composition
                } else {
                    state.compositions.push(composition)
                }
            } else {
                state.compositions.push(composition);
            }

            this.commit('SET_TOTAL_LENGTH');
            this.commit('SET_TOTAL_COST');
        },
        SET_TOTAL_LENGTH(state) {
            let length = 0;
            if (state.compositions.length) {
                state.compositions.forEach(composition => {
                    if (composition.items.length) {
                        composition.items.forEach(item => {
                            length = parseFloat(length) + parseFloat(item.totalLength);
                            // if (item.pivot) {
                            //     length = parseFloat(length) + (parseFloat(item.length) * 1000 * parseFloat(item.pivot.useful));
                            // }
                        });
                    }
                });
            }
            state.totalLength = length.toFixed(2);
        },
        SET_TOTAL_COST(state) {
            let cost = 0;
            if (state.compositions.length) {
                state.compositions.forEach(composition => {
                    if (composition.items.length) {
                        composition.items.forEach(item => {
                            // cost = parseFloat(cost) + parseFloat(item.totalCost);
                            if (item.pivot) {
                                if (composition.name == 'attachments' || composition.name == 'containers') {
                                    cost = parseFloat(cost) + (parseFloat(item.cost_unit) * parseFloat(item.pivot.useful));
                                } else if (composition.name == 'raws') {
                                    cost = parseFloat(cost) + (parseFloat(item.cost_portion) * parseFloat(item.pivot.useful));
                                } else if (composition.name == 'goods') {
                                    cost = parseFloat(cost) + (parseFloat(item.article.cost_default) * parseFloat(item.pivot.useful));
                                } else {
                                    return 0;
                                }
                            }
                        });
                    }
                });
            }
            state.totalCost = cost.toFixed(2);
        }

    },
    getters: {
        SERVICES_TOTAL_LENGTH: state => {
            return state.totalLength;
        },
        SERVICES_TOTAL_COST: state => {
            return state.totalCost;
        },
    }
};

export default moduleGoods;
