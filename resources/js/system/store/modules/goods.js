const modulePromotion = {
    state: {
        compositions: [],
    },
    mutations: {
        SET_COMPOSITION(state, composition) {
            if (state.compositions.length) {
                var index = state.compositions.findIndex(obj => obj.name == composition.name);
                if (state.compositions[index]) {
                    state.compositions[index] = composition
                } else {
                    state.compositions.push(composition)
                };
            } else {
                state.compositions.push(composition);
            }
        },
    },
    getters: {
        totalWeight: state => {
            var weight = 0;
            if (state.compositions.length) {
                state.compositions.forEach(composition => {
                    if (composition.items.length) {
                        composition.items.forEach(item => {
                            if (item.pivot) {
                                weight = parseFloat(weight) + (parseFloat(item.weight) * 1000 * parseFloat(item.pivot.useful));
                            }
                        });
                    }
                });
            }
            return weight.toFixed(2);
        },
        totalCost: state => {
            var cost = 0;
            if (state.compositions.length) {
                state.compositions.forEach(composition => {
                    if (composition.items.length) {
                        composition.items.forEach(item => {
                            if (item.pivot) {
                                if (composition.name == 'attachments' || composition.name == 'containers') {
                                    cost = parseFloat(cost) + (parseFloat(item.cost_unit) * parseFloat(item.pivot.useful));
                                } else if (composition.name == 'raws') {
                                            cost = parseFloat(cost) + (parseFloat(item.cost_portion) * parseFloat(item.pivot.useful));
                                }
                            }
                        });
                    }
                });
            }
            return cost.toFixed(2);
        },

    }

};

export default modulePromotion;
