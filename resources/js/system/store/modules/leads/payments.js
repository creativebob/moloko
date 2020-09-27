const moduleLead = {
    state: {

    },
    mutations: {

        // Платежи
        ADD_PAYMENT(state, payment) {
            state.estimate.payments.push(payment);
        }
    },
    actions: {

    },
    getters: {
        // Платежи
        paymentsAmount: state => {
            let amount = 0;
            if (state.estimate.payments.length) {
                state.estimate.payments.forEach(function(item) {
                    return amount += Number(item.amount)
                });
            }
            return amount;
        }
    }
};

export default moduleLead;
