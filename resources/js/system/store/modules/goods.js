const moduleGoods = {
    state: {
        compositions: [],
        totalWeight: 0,
        totalVolume: 0,
        totalCost: 0,
        totalCostDefault: 0,
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
            this.commit('SET_TOTAL_VOLUME');
            this.commit('SET_TOTAL_COST');
            this.commit('SET_TOTAL_COST_DEFAULT');
        },
        SET_TOTAL_WEIGHT(state) {
            let weight = 0;
            if (state.compositions.length) {
                state.compositions.forEach(composition => {
                    if (composition.items.length) {
                        composition.items.forEach(item => {

                            weight = parseFloat(weight) + parseFloat(item.totalWeight);
                            
                        });
                    }
                });
            }
            state.totalWeight = weight.toFixed(2);
        },
        SET_TOTAL_VOLUME(state) {
            let volume = 0;
            if (state.compositions.length) {
                state.compositions.forEach(composition => {
                    if (composition.items.length) {
                        composition.items.forEach(item => {

                            volume = parseFloat(volume) + parseFloat(item.totalVolume);
                            
                        });
                    }
                });
            }
            state.totalVolume = volume.toFixed(2);
        },
        SET_TOTAL_COST(state) {
            let cost = 0;
            if (state.compositions.length) {
                state.compositions.forEach(composition => {
                    if (composition.items.length) {
                        composition.items.forEach(item => {

                            cost = parseFloat(cost) + (parseFloat(item.cost_portion) * parseFloat(item.pivot.useful));

                        });
                    }
                });
            }
            state.totalCost = cost.toFixed(2);
        },
        SET_TOTAL_COST_DEFAULT(state) {
            let costDefault = 0;
            if (state.compositions.length) {
                state.compositions.forEach(composition => {
                    if (composition.items.length) {
                        composition.items.forEach(item => {

                            costDefault = parseFloat(costDefault) + (parseFloat(item.article.cost_default) * parseFloat(item.pivot.useful));

                        });
                    }
                });
            }
            state.totalCostDefault = costDefault.toFixed(2);
        }

    },
    getters: {
        totalWeight: state => {
            return state.totalWeight;
        },
        totalVolume: state => {
            return state.totalVolume;
        },
        totalCost: state => {
            return state.totalCost;
        },
        totalCostDefault: state => {
            return state.totalCostDefault;
        },
    }
};

export default moduleGoods;
