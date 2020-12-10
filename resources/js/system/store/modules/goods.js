const moduleGoods = {
    state: {
        compositions: [],
        totalWeight: 0,
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

            this.commit('SET_TOTAL_WEIGHT');
            this.commit('SET_TOTAL_COST');
        },
        SET_TOTAL_WEIGHT(state) {
            let weight = 0;
            if (state.compositions.length) {
                state.compositions.forEach(composition => {
                    if (composition.items.length) {
                        composition.items.forEach(item => {
                            weight = parseFloat(weight) + parseFloat(item.totalWeight);
                            // if (item.pivot) {
                            //     weight = parseFloat(weight) + (parseFloat(item.weight) * 1000 * parseFloat(item.pivot.useful));
                            // }
                        });
                    }
                });
            }
            state.totalWeight = weight.toFixed(2);
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
        totalWeight: state => {
            return state.totalWeight;
        },
        totalCost: state => {
            return state.totalCost;
        },
    }
};

export default moduleGoods;
