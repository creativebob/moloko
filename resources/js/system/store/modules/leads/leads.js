const moduleLead = {
    state: {
        users: [],
        companies: [],

        lead: null,
        client: null,

        outlet: null,
        outletSettings: [],

        catalogGoodsId: null,
        catalogsGoodsIds: [],

        catalogServicesId: null,
        catalogsServicesIds: [],

        estimate: null,

        goodsItems: [],
        needProduction: false,

        servicesItems: [],

        stock: null,

        payments: [],
        paymentsMethods: [],
        paymentsMethodId: null,

        agent: null,
        countAgents: 0,

        userFilials: [],
        userOutlets: [],

        labels: [],

        change: false,
        loading: false,

        errors: [],

        needChangeTabToEstimate: false
    },
    mutations: {
        SET_USERS(state, users) {
            state.users = users;
        },
        SET_COMPANIES(state, companies) {
            state.companies = companies;
        },

        // Лид
        SET_LEAD(state, lead) {
            if (lead.main_phones.length) {
                lead.main_phone = lead.main_phones[0].phone;
            } else {
                lead.main_phone = null;
            }

            state.lead = lead;
        },
        UPDATE_LEAD_PERSONAL(state, lead) {
            state.lead.main_phone = lead.main_phone;
            state.lead.name = lead.name;
            state.lead.company_name = lead.company_name;
            state.lead.location.city_id = lead.location.city_id;
            state.lead.location.address = lead.location.address;
            state.lead.email = lead.email;

            state.lead.user_id = lead.user_id;
            state.lead.organization_id = lead.organization_id;
            state.lead.client_id = lead.client_id;

            this.commit('SET_CHANGE');
        },
        UPDATE_LEAD_EVENT(state, lead) {
            state.lead.stage_id = lead.stage_id;
            state.lead.shipment_at = lead.shipment_at;

            this.commit('SET_CHANGE');
        },
        UPDATE_LEAD_FILIAL(state, lead) {
            state.lead.filial_id = lead.filial_id;
            state.lead.outlet_id = lead.outlet_id;

            this.commit('SET_CHANGE');
        },
        UPDATE_LEAD_DESC(state, desc) {
            state.lead.description = desc;

            this.commit('SET_CHANGE');
        },

        CHANGE_LABELS(state, data) {
            if (data.status == true) {
                state.labels.push(data.id)
            } else {
                const index = state.labels.findIndex(obj => obj == data.id);
                state.labels.splice(index, 1);
            }

            this.commit('SET_CHANGE');
        },

        // Клиент
        SET_CLIENT(state, client) {
            if (client) {
                switch (client.clientable_type) {
                    case ('App\\Company'):
                        state.lead.organization_id = client.clientable_id;
                        break;
                    case ('App\\User'):
                        state.lead.user_id = client.clientable_id;
                        break;
                }
                state.lead.client_id = client.id;
            }
            state.client = client ? client : null;

            this.commit('UPDATE_GOODS_ITEMS');
        },

        // Торговая точка
        SET_OUTLET(state, outlet) {
            state.outlet = outlet;
        },
        SET_OUTLET_SETTINGS(state, settings) {
            state.outletSettings = settings;
        },

        SET_USER_FILIALS(state, filials) {
            state.userFilials = filials;
        },
        SET_USER_OUTLETS(state, outlets) {
            state.userOutlets = outlets;
        },

        // Каталоги
        SET_CATALOG_GOODS_ID(state, id) {
            state.catalogGoodsId = id;
        },
        SET_CATALOG_SERVICES_ID(state, id) {
            state.catalogServicesId = id;
        },

        // Способы платежа
        SET_PAYMENTS_METHODS(state, paymentsMethods) {
            state.paymentsMethods = paymentsMethods;

            this.commit('SET_PAYMENTS_METHOD_ID');
        },
        SET_PAYMENTS_METHOD_ID(state, id = null) {
            if (id) {
                state.paymentsMethodId = id;
            } else {
                if (state.paymentsMethods.length) {
                    const fullPayment = state.paymentsMethods.find(obj => obj.alias === 'full_payment');
                    if (fullPayment) {
                        state.paymentsMethodId = fullPayment.id
                    } else {
                        state.paymentsMethodId = state.paymentsMethods[0].id
                    }
                }
            }
        },

        SET_STOCK(state, stock) {
            state.stock = stock;
        },

        // Смета
        SET_ESTIMATE(state, estimate) {
            state.estimate = estimate;

            state.catalogsGoodsIds = [];
            estimate.catalogs_goods.forEach(catalog => {
                state.catalogsGoodsIds.push(catalog.id);
            })

            state.catalogsServicesIds = [];
            estimate.catalogs_services.forEach(catalog => {
                state.catalogsServicesIds.push(catalog.id);
            })
        },

        CHANGE_NEED_CHANGE_TAB_TO_ESTIMATE(state, val = false) {
            state.needChangeTabToEstimate = val;
        },

        // Товары
        SET_GOODS_ITEMS(state, goodsItems) {
            state.goodsItems = goodsItems;

            this.commit('CHECK_NEED_PRODUCTION');
        },

        ADD_GOODS_ITEM_TO_ESTIMATE(state, price) {
            if (!state.estimate.registered_at) {
                this.commit('SET_CHANGE');

                // TODO - 25.09.20 - Нужна будет проверка на серийность
                let index = state.goodsItems.findIndex(obj => obj.price_id == price.id);
                if (index > -1) {
                    let item = state.goodsItems[index];
                    item.count = parseFloat(item.count) + 1;
                    Vue.set(state.goodsItems, index, item);

                    this.commit('SET_GOODS_ITEM_AGGREGATIONS', index);
                } else {
                    let item = {
                        id: state.goodsItems.length + 1,

                        estimate_id: state.estimate.id,

                        price_id: price.id,
                        price_goods: price,

                        goods_id: price.goods_id,
                        goods: price.goods,

                        currency_id: price.currency_id,
                        currency: price.currency,

                        sale_mode: 1,
                        comment: null,

                        cost_unit: parseFloat(price.goods.article.cost_default),
                        price: parseFloat(price.price),
                        points: price.points,
                        count: "1.00",

                        price_discount_id: price.price_discount_id,
                        price_discount_unit: parseFloat(price.price_discount),

                        catalogs_item_discount_id: price.catalogs_item_discount_id,
                        catalogs_item_discount_unit: parseFloat(price.catalogs_item_discount),

                        estimate_discount_id: price.estimate_discount_id,
                        estimate_discount_unit: parseFloat(price.estimate_discount),

                        manual_discount_currency: 0,
                        manual_discount_percent: 0,

                        is_manual: 0,

                        stock_id: state.stock.id,

                        company_id: null,
                    };
                    state.goodsItems.push(item);
                    index = state.goodsItems.findIndex(obj => obj.id == item.id);

                    this.commit('SET_GOODS_ITEM_AGGREGATIONS', index);

                    const found = state.catalogsGoodsIds.find(obj => obj == price.catalogs_goods_id);
                    if (!found) {
                        state.catalogsGoodsIds.push(price.catalogs_goods_id);
                    }

                    this.commit('CHECK_NEED_PRODUCTION');
                }
            }
        },

        UPDATE_GOODS_ITEM_COMMENT(state, data) {
            this.commit('SET_CHANGE');
            const index = state.goodsItems.findIndex(obj => obj.id === data.id);
            let item = state.goodsItems[index];
            item.comment = data.comment;
            Vue.set(state.goodsItems, index, item);
        },
        UPDATE_GOODS_ITEM_COUNT(state, data) {
            this.commit('SET_CHANGE');
            const index = state.goodsItems.findIndex(obj => obj.id === data.id);
            let item = state.goodsItems[index];
            item.count = data.count;
            Vue.set(state.goodsItems, index, item);

            this.commit('SET_GOODS_ITEM_AGGREGATIONS', index);
        },
        UPDATE_GOODS_ITEM_IS_MANUAL(state, data) {
            this.commit('SET_CHANGE');
            const index = state.goodsItems.findIndex(obj => obj.id === data.id);
            let item = state.goodsItems[index];

            item.manual_discount_currency = data.manual_discount_currency;
            item.manual_discount_percent = data.manual_discount_percent;
            item.is_manual = data.is_manual;

            item.count = data.count;

            Vue.set(state.goodsItems, index, item);

            this.commit('SET_GOODS_ITEM_AGGREGATIONS', index);
        },

        UPDATE_GOODS_ITEM(state, item) {
            const index = state.goodsItems.findIndex(obj => obj.id === item.id);
            Vue.set(state.goodsItems, index, item);

            this.commit('SET_GOODS_ITEM_AGGREGATIONS', index);
        },

        SET_GOODS_ITEM_AGGREGATIONS(state, index) {
            let item = state.goodsItems[index];

            const count = parseFloat(item.count);
            item.count = parseFloat(item.count).toFixed(2);

            switch (item.sale_mode) {
                case (1):
                    item.total_points = 0;
                    item.total_bonuses = 0;

                    // Основные расчеты
                    item.cost = item.cost_unit * count;
                    item.amount = parseFloat(item.price) * count;

                    // Скидки
                    // Если есть ручная скидка
                    if (item.is_manual == 1) {

                        item.price_discount = 0;
                        item.total_price_discount = item.amount;

                        item.catalogs_item_discount = 0;
                        item.total_catalogs_item_discount = item.amount;

                        item.estimate_discount = 0;
                        item.total_estimate_discount = item.amount;

                        item.client_discount_currency = 0;
                        item.total_client_discount = item.amount;

                        item.total_manual_discount = item.amount - (item.manual_discount_currency * count);
                        item.total = item.total_manual_discount;

                        item.total_computed_discount = 0;

                        item.discount_currency = item.manual_discount_currency * count;
                        item.discount_percent = item.manual_discount_percent;
                    } else {
                        // Иначе рассчитываем
                        item.price_discount = item.price_discount_unit * count;
                        item.total_price_discount = item.amount - item.price_discount;

                        item.catalogs_item_discount = item.catalogs_item_discount_unit * count;
                        item.total_catalogs_item_discount = item.total_price_discount - item.catalogs_item_discount;

                        item.estimate_discount = item.estimate_discount_unit * count;
                        item.total_estimate_discount = item.total_catalogs_item_discount - item.estimate_discount;

                        item.client_discount_percent = state.client ? state.client.discount : 0;

                        if (item.client_discount_percent > 0) {
                            item.client_discount_unit_currency = item.total_estimate_discount / 100 * item.client_discount_percent / count;
                            item.client_discount_currency = item.client_discount_unit_currency * count;
                        } else {
                            item.client_discount_unit_currency = 0;
                            item.client_discount_currency = 0;
                        }
                        item.total_client_discount = item.total_estimate_discount - item.client_discount_currency;

                        item.total = item.total_client_discount;

                        item.discount_currency = item.amount - item.total;
                        if (item.discount_currency > 0) {
                            item.discount_percent = item.discount_currency * 100 / item.amount;
                        } else {
                            item.discount_percent = 0;
                        }

                        item.computed_discount_percent = item.discount_percent;
                        item.computed_discount_currency = item.discount_currency / count;
                        item.total_computed_discount = item.discount_currency;

                        item.manual_discount_currency = 0;
                        item.manual_discount_percent = 0;
                        item.total_manual_discount = 0;
                    }

                    // Маржа
                    let totalPrice = 0;
                    if (item.is_manual == 0) {
                        totalPrice = parseFloat(item.price) - item.price_discount_unit - item.catalogs_item_discount_unit - item.estimate_discount_unit - item.client_discount_unit_currency;
                    } else {
                        totalPrice = parseFloat(item.price) - item.manual_discount_currency;
                    }
                    item.margin_currency_unit = totalPrice - item.cost_unit;
                    item.margin_currency = item.total - item.cost;

                    if (item.total > 0) {
                        item.margin_percent_unit = (item.margin_currency_unit / totalPrice * 100);
                        item.margin_percent = (item.margin_currency / item.total * 100);
                    } else {
                        item.margin_percent_unit = (item.margin_currency_unit * 100);
                        item.margin_percent = (item.margin_currency * 100);
                    }
                    break;

                case (2):
                    item.amount = 0;

                    item.price_discount = 0;
                    item.total_price_discount = 0;

                    item.catalogs_item_discount = 0;
                    item.total_catalogs_item_discount = 0;

                    item.estimate_discount = 0;
                    item.total_estimate_discount = 0;

                    item.manual_discount_currency = 0;
                    item.manual_discount_percent = 0;
                    item.total_manual_discount = 0;

                    item.client_discount_currency = 0;
                    item.total_client_discount = 0;

                    item.computed_discount_percent = 0;
                    item.computed_discount_currency = 0;
                    item.total_computed_discount = 0;

                    item.total = 0;
                    item.total_bonuses = 0;
                    item.total_points = item.points * count;

                    item.discount_currency = 0;
                    item.discount_percent = 0;

                    item.margin_currency = 0;
                    item.margin_percent = 0;
                    break;
            }

            Vue.set(state.goodsItems, index, item);
        },

        REMOVE_GOODS_ITEM(state, id) {
            const index = state.goodsItems.findIndex(obj => obj.id === id);

            const catalogsGoodsId = state.goodsItems[index].price_goods.catalogs_goods_id;

            state.goodsItems.splice(index, 1);

            if (state.catalogsGoodsIds.length > 1) {
                const found = state.goodsItems.find(obj => obj.price_goods.catalogs_goods_id == catalogsGoodsId);
                if (!found) {
                    const index = state.catalogsGoodsIds.findIndex(obj => obj === catalogsGoodsId);
                    state.catalogsGoodsIds.splice(index, 1);
                }
            }

            if (state.goodsItems.length == 0) {
                state.catalogsGoodsIds = [];
            }

            this.commit('SET_CHANGE');

            this.commit('CHECK_NEED_PRODUCTION');
        },
        UPDATE_GOODS_ITEMS(state) {
            state.goodsItems.forEach(item => {
                const index = state.goodsItems.findIndex(obj => obj.id === item.id);
                this.commit('SET_GOODS_ITEM_AGGREGATIONS', index);
            });

            this.commit('CHECK_NEED_PRODUCTION');
        },

        CHECK_NEED_PRODUCTION(state) {
            let countNeedProduced = 0,
                countProduced = 0;
            state.goodsItems.forEach(item => {
                if (item.goods.is_produced == 1) {
                    countNeedProduced++;
                    if (item.productions_item) {
                        countProduced++
                    }
                }
            });
            state.needProduction = (countNeedProduced !== countProduced);
        },

        // Услуги
        SET_SERVICES_ITEMS(state, servicesItems) {
            state.servicesItems = servicesItems;
        },

        ADD_SERVICE_ITEM_TO_ESTIMATE(state, price) {
            if (!state.estimate.registered_at) {
                this.commit('SET_CHANGE');

                // TODO - 25.09.20 - Нужна будет проверка на серийность
                let index = state.servicesItems.findIndex(obj => obj.price_id == price.id);
                if (index > -1) {
                    let item = state.servicesItems[index];
                    item.count = parseFloat(item.count) + 1;
                    Vue.set(state.servicesItems, index, item);

                    this.commit('SET_SERVICE_ITEM_AGGREGATIONS', index);
                } else {
                    let item = {
                        id: state.servicesItems.length + 1,

                        estimate_id: state.estimate.id,

                        price_id: price.id,
                        price_service: price,

                        service_id: price.service_id,
                        service: price.service,

                        currency_id: price.currency_id,
                        currency: price.currency,

                        sale_mode: 1,
                        comment: null,

                        cost_unit: parseFloat(price.service.process.cost_default),
                        price: parseFloat(price.price),
                        points: price.points,
                        count: "1.00",

                        price_discount_id: price.price_discount_id,
                        price_discount_unit: parseFloat(price.price_discount),

                        catalogs_item_discount_id: price.catalogs_item_discount_id,
                        catalogs_item_discount_unit: parseFloat(price.catalogs_item_discount),

                        estimate_discount_id: price.estimate_discount_id,
                        estimate_discount_unit: parseFloat(price.estimate_discount),

                        manual_discount_currency: 0,
                        manual_discount_percent: 0,

                        is_manual: 0,

                        company_id: null,
                    };
                    state.servicesItems.push(item);
                    index = state.servicesItems.findIndex(obj => obj.id == item.id);

                    this.commit('SET_SERVICE_ITEM_AGGREGATIONS', index);

                    const found = state.catalogsServicesIds.find(obj => obj == price.catalogs_service_id);
                    if (!found) {
                        state.catalogsServicesIds.push(price.catalogs_service_id);
                    }
                }
            }
        },

        UPDATE_SERVICE_ITEM_COMMENT(state, data) {
            this.commit('SET_CHANGE');
            const index = state.servicesItems.findIndex(obj => obj.id === data.id);
            let item = state.servicesItems[index];
            item.comment = data.comment;
            Vue.set(state.servicesItems, index, item);
        },
        UPDATE_SERVICE_ITEM_COUNT(state, data) {
            this.commit('SET_CHANGE');
            const index = state.servicesItems.findIndex(obj => obj.id === data.id);
            let item = state.servicesItems[index];
            item.count = data.count;
            Vue.set(state.servicesItems, index, item);

            this.commit('SET_SERVICE_ITEM_AGGREGATIONS', index);
        },
        UPDATE_SERVICE_ITEM_IS_MANUAL(state, data) {
            this.commit('SET_CHANGE');
            const index = state.servicesItems.findIndex(obj => obj.id === data.id);
            let item = state.servicesItems[index];

            item.manual_discount_currency = data.manual_discount_currency;
            item.manual_discount_percent = data.manual_discount_percent;
            item.is_manual = data.is_manual;

            item.count = data.count;

            Vue.set(state.servicesItems, index, item);

            this.commit('SET_SERVICE_ITEM_AGGREGATIONS', index);
        },

        UPDATE_SERVICE_ITEM(state, item) {
            const index = state.servicesItems.findIndex(obj => obj.id === item.id);
            Vue.set(state.servicesItems, index, item);

            this.commit('SET_SERVICE_ITEM_AGGREGATIONS', index);
        },

        SET_SERVICE_ITEM_AGGREGATIONS(state, index) {
            let item = state.servicesItems[index];

            const count = parseFloat(item.count);
            item.count = parseFloat(item.count).toFixed(2);

            switch (item.sale_mode) {
                case (1):
                    item.total_points = 0;
                    item.total_bonuses = 0;

                    // Основные расчеты
                    item.cost = item.cost_unit * count;
                    item.amount = parseFloat(item.price) * count;

                    // Скидки
                    // Если есть ручная скидка
                    if (item.is_manual == 1) {

                        item.price_discount = 0;
                        item.total_price_discount = item.amount;

                        item.catalogs_item_discount = 0;
                        item.total_catalogs_item_discount = item.amount;

                        item.estimate_discount = 0;
                        item.total_estimate_discount = item.amount;

                        item.client_discount_currency = 0;
                        item.total_client_discount = item.amount;

                        item.total_manual_discount = item.amount - (item.manual_discount_currency * count);
                        item.total = item.total_manual_discount;

                        item.total_computed_discount = 0;

                        item.discount_currency = item.manual_discount_currency * count;
                        item.discount_percent = item.manual_discount_percent;
                    } else {
                        // Иначе рассчитываем
                        item.price_discount = item.price_discount_unit * count;
                        item.total_price_discount = item.amount - item.price_discount;

                        item.catalogs_item_discount = item.catalogs_item_discount_unit * count;
                        item.total_catalogs_item_discount = item.total_price_discount - item.catalogs_item_discount;

                        item.estimate_discount = item.estimate_discount_unit * count;
                        item.total_estimate_discount = item.total_catalogs_item_discount - item.estimate_discount;

                        item.client_discount_percent = state.client ? state.client.discount : 0;

                        if (item.client_discount_percent > 0) {
                            item.client_discount_unit_currency = item.total_estimate_discount / 100 * item.client_discount_percent / count;
                            item.client_discount_currency = item.client_discount_unit_currency * count;
                        } else {
                            item.client_discount_unit_currency = 0;
                            item.client_discount_currency = 0;
                        }
                        item.total_client_discount = item.total_estimate_discount - item.client_discount_currency;

                        item.total = item.total_client_discount;

                        item.discount_currency = item.amount - item.total;
                        if (item.discount_currency > 0) {
                            item.discount_percent = item.discount_currency * 100 / item.amount;
                        } else {
                            item.discount_percent = 0;
                        }

                        item.computed_discount_percent = item.discount_percent;
                        item.computed_discount_currency = item.discount_currency / count;
                        item.total_computed_discount = item.discount_currency;

                        item.manual_discount_currency = 0;
                        item.manual_discount_percent = 0;
                        item.total_manual_discount = 0;
                    }

                    // Маржа
                    let totalPrice = 0;
                    if (item.is_manual == 0) {
                        totalPrice = parseFloat(item.price) - item.price_discount_unit - item.catalogs_item_discount_unit - item.estimate_discount_unit - item.client_discount_unit_currency;
                    } else {
                        totalPrice = parseFloat(item.price) - item.manual_discount_currency;
                    }
                    item.margin_currency_unit = totalPrice - item.cost_unit;
                    item.margin_currency = item.total - item.cost;

                    if (item.total > 0) {
                        item.margin_percent_unit = (item.margin_currency_unit / totalPrice * 100);
                        item.margin_percent = (item.margin_currency / item.total * 100);
                    } else {
                        item.margin_percent_unit = (item.margin_currency_unit * 100);
                        item.margin_percent = (item.margin_currency * 100);
                    }
                    break;

                case (2):
                    item.amount = 0;

                    item.price_discount = 0;
                    item.total_price_discount = 0;

                    item.catalogs_item_discount = 0;
                    item.total_catalogs_item_discount = 0;

                    item.estimate_discount = 0;
                    item.total_estimate_discount = 0;

                    item.manual_discount_currency = 0;
                    item.manual_discount_percent = 0;
                    item.total_manual_discount = 0;

                    item.client_discount_currency = 0;
                    item.total_client_discount = 0;

                    item.computed_discount_percent = 0;
                    item.computed_discount_currency = 0;
                    item.total_computed_discount = 0;

                    item.total = 0;
                    item.total_bonuses = 0;
                    item.total_points = item.points * count;

                    item.discount_currency = 0;
                    item.discount_percent = 0;

                    item.margin_currency = 0;
                    item.margin_percent = 0;
                    break;
            }

            Vue.set(state.servicesItems, index, item);
        },

        REMOVE_SERVICE_ITEM(state, id) {
            const index = state.servicesItems.findIndex(obj => obj.id === id);

            const catalogsServiceId = state.servicesItems[index].price_service.catalogs_service_id;

            state.servicesItems.splice(index, 1);

            if (state.catalogsServicesIds.length > 1) {
                const found = state.servicesItems.find(obj => obj.price_service.catalogs_service_id == catalogsServiceId);
                if (!found) {
                    const index = state.catalogsServicesIds.findIndex(obj => obj === catalogsServiceId);
                    state.catalogsServicesIds.splice(index, 1);
                }
            }

            if (state.servicesItems.length == 0) {
                state.catalogsServicesIds = [];
            }

            this.commit('SET_CHANGE');
        },
        UPDATE_SERVICES_ITEMS(state) {
            state.servicesItems.forEach(item => {
                const index = state.servicesItems.findIndex(obj => obj.id === item.id);
                this.commit('SET_SERVICE_ITEM_AGGREGATIONS', index);
            });
        },


        // Агенты
        SET_AGENT(state, agent) {
            state.agent = agent;
        },

        SET_COUNT_AGENTS(state, count = 0) {
            state.countAgents = count;
        },

        // Изменения
        SET_CHANGE(state) {
            state.change = true;
            state.needChangeTabToEstimate = true;
        },


        SET_PAYMENTS(state, payments) {
            state.payments = payments;
        },

        // Скидки
        REMOVE_DISCOUNT(state, id) {
            this.commit('SET_CHANGE');

            const index = state.estimate.discounts.findIndex(obj => obj.id === id);
            state.estimate.discounts.splice(index, 1);

            state.goodsItems.forEach(item => {
                if (item.estimate_discount_id == id) {

                    item.estimate_discount_id = null;
                    item.estimate_discount_unit = 0;

                    const index = state.goodsItems.findIndex(obj => obj.id == item.id);

                    Vue.set(state.goodsItems, index, item);

                    this.commit('SET_GOODS_ITEM_AGGREGATIONS', index);
                }
            })
        }
    },
    actions: {
        // Обновление лида и сметы
        UPDATE({state}, data) {
            state.loading = true;
            // console.log(data);

            axios
                .patch('/admin/leads/axios_update/' + state.lead.id, data)
                .then(response => {
                    // console.log(response.data);

                    const lead = response.data.lead;
                    this.commit('SET_LEAD', lead);

                    if (response.data.estimate.registered_at === null) {
                        if (lead.user) {
                            const user = lead.user;
                            const index = state.users.findIndex(obj => obj.id == user.id);
                            if (index > -1) {
                                Vue.set(state.users, index, user);
                            } else {
                                state.users.push(user);
                            }
                        }

                        if (lead.organization) {
                            const organization = lead.organization;
                            const index = state.companies.findIndex(obj => obj.id == organization.id);
                            if (index > -1) {
                                Vue.set(state.companies, index, organization);
                            } else {
                                state.companies.push(organization);
                            }
                        }
                    }

                    if (lead.client) {
                        this.commit('SET_CLIENT', lead.client);
                    }

                    this.commit('SET_ESTIMATE', response.data.estimate);

                    this.commit('SET_GOODS_ITEMS', response.data.goods_items);
                    this.commit('SET_SERVICES_ITEMS', response.data.services_items);

                    if (response.data.outlet) {
                        this.commit('SET_OUTLET', response.data.outlet);
                        this.commit('SET_OUTLET_SETTINGS', response.data.outlet.settings);
                    }

                    state.change = false;
                })
                .catch(error => {
                    console.log(error)
                })
                .finally(() => (state.loading = false));
        },

        // Резервы
        // Смета
        RESERVE_ESTIMATE({state}) {
            state.loading = true;
            axios
                .post('/admin/estimates/' + state.estimate.id + '/reserving')
                .then(response => {
                    // console.log(response.data);
                    if (response.data.msg.length > 0) {
                        let msg = '';
                        response.data.msg.forEach(item => {
                            if (item !== null) {
                                msg = msg + '- ' + item + '\r\n';
                            }
                        });
                        if (msg !== '') {
                            alert(msg);
                        }
                    }
                    this.commit('SET_GOODS_ITEMS', response.data.items);
                })
                .catch(error => {
                    console.log(error)
                })
                .finally(() => (state.loading = false));
        },
        CANCEL_RESERVE_ESTIMATE({state}) {
            state.loading = true;
            axios
                .post('/admin/estimates/' + state.estimate.id + '/unreserving')
                .then(response => {
                    if (response.data.msg.length > 0) {
                        let msg = '';
                        response.data.msg.forEach(item => {
                            if (item !== null) {
                                msg = msg + '- ' + item + '\r\n';
                            }
                        });
                        if (msg !== '') {
                            alert(msg);
                        }
                    }
                    this.commit('SET_GOODS_ITEMS', response.data.items);
                })
                .catch(error => {
                    console.log(error)
                })
                .finally(() => (state.loading = false));
        },

        // Пункт сметы
        RESERVE_GOODS_ITEM({state}, id) {
            state.loading = true;
            const index = state.goodsItems.findIndex(obj => obj.id === id);
            let item = state.goodsItems[index];
            axios
                .post('/admin/estimates_goods_items/' + id + '/reserving', {
                    count: item.count
                })
                .then(response => {
                    if (response.data.msg !== null) {
                        alert(response.data.msg);
                    }
                    Vue.set(state.goodsItems, index, response.data.item);
                })
                .catch(error => {
                    console.log(error)
                })
                .finally(() => (state.loading = false));
        },
        CANCEL_RESERVE_GOODS_ITEM({state}, id) {
            state.loading = true;
            const index = state.goodsItems.findIndex(obj => obj.id === id);
            axios
                .post('/admin/estimates_goods_items/' + id + '/unreserving')
                .then(response => {
                    if (response.data.msg !== null) {
                        alert(response.data.msg);
                    }
                    Vue.set(state.goodsItems, index, response.data.item);
                })
                .catch(error => {
                    console.log(error)
                })
                .finally(() => (state.loading = false));
        },

        // Отмена регистрации
        UNREGISTER_ESTIMATE({state}) {
            state.loading = true;
            axios
                .post('/admin/estimates/' + state.estimate.id + '/unregistering')
                .then(response => {
                    this.commit('SET_ESTIMATE', response.data);
                    this.commit('SET_AGENT', null);
                })
                .catch(error => {
                    console.log(error)
                })
                .finally(() => (state.loading = false));
        },

        // Устанавливаем агента для сметы
        SET_AGENT_FOR_ESTIMATE({state}) {
            state.loading = true;
            axios
                .post('/admin/estimates/set-agent', {
                    estimate_id: state.estimate.id,
                    catalog_goods_id: state.catalogGoodsId,
                    catalog_services_id: state.catalogServicesId,
                    agent_id: state.agent.id,
                })
                .then(response => {
                    if (response.data.success) {
                        this.commit('SET_ESTIMATE', response.data.estimate);
                        this.commit('SET_GOODS_ITEMS', response.data.estimate.goods_items);
                    }
                })
                .catch(error => {
                    console.log(error)
                })
                .finally(() => (state.loading = false));
        },

        /**
         * Производство сметы
         *
         * @param state
         * @constructor
         */
        PRODUCED_ESTIMATE({state}) {
            state.loading = true;
            axios
                .post('/admin/estimates/' + state.estimate.id + '/producing')
                .then(response => {
                    if (response.data.success) {
                        this.commit('SET_ESTIMATE', response.data.estimate);

                        this.commit('SET_GOODS_ITEMS', response.data.estimate.goods_items);
                    } else {
                        console.log(response.data.errors);
                        state.errors = response.data.errors;
                    }
                })
                .catch(error => {
                    console.log(error)
                })
                .finally(() => (state.loading = false));
        },

        /**
         * Продажа сметы
         *
         * @param state
         * @constructor
         */
        CONDUCTED_ESTIMATE({state}) {
            state.loading = true;
            axios
                .post('/admin/estimates/' + state.estimate.id + '/conducting')
                .then(response => {
                    if (response.data.success) {
                        this.commit('SET_ESTIMATE', response.data.estimate);
                    } else {
                        console.log(response.data.errors);
                        state.errors = response.data.errors;
                    }
                })
                .catch(error => {
                    console.log(error)
                })
                .finally(() => (state.loading = false));
        },

        /**
         * Списание сметы
         *
         * @param state
         * @param data
         * @constructor
         */
        DISMISSED_ESTIMATE({state}, data) {
            state.loading = true;
            axios
                .post('/admin/estimates/' + state.estimate.id + '/dismissing', data)
                .then(response => {
                    if (response.data.success) {
                        this.commit('SET_ESTIMATE', response.data.estimate);

                        this.commit('SET_GOODS_ITEMS', response.data.goods_items);
                        this.commit('SET_SERVICES_ITEMS', response.data.services_items);
                    } else {
                        console.log(response.data.errors);
                        state.errors = response.data.errors;
                    }
                })
                .catch(error => {
                    console.log(error)
                })
                .finally(() => (state.loading = false));
        },

        /**
         * Получение торговой точки по id
         *
         * @param state
         * @param id
         * @constructor
         */
        GET_OUTLET({state}, id) {
            state.loading = true;
            axios
                .post('/admin/outlets/get_by_id', {
                    id: id
                })
                .then(response => {
                    this.commit('SET_OUTLET', response.data);
                })
                .catch(error => {
                    console.log(error)
                })
                .finally(() => (state.loading = false));
        },

        // Платежи
        ADD_PAYMENT({state, getters}, data) {
            state.loading = true;

            // TODO - 16.10.20 - Избавиться от харкода
            data.currency_id = 1;
            data.contract_id = state.client.contract.id;
            data.contract_type = 'App\\ContractsClient';
            data.document_id = state.lead.estimate.id;
            data.document_type = 'App\\Models\\System\\Documents\\Estimate';

            const fullPayment = state.paymentsMethods.find(obj => obj.alias === 'full_payment');

            if (data.payments_method_id != fullPayment.id) {
                // Проверка на полную предоплату
                const prepayment = state.paymentsMethods.find(obj => obj.alias === 'partial_prepayment');
                if (prepayment) {
                    let total = data.cash + data.electronically;
                    if (total >= state.estimate.total) {
                        const fullPrepayment = state.paymentsMethods.find(obj => obj.alias === 'full_prepayment');
                        if (fullPrepayment) {
                            data.payments_method_id = fullPrepayment.id;
                        }
                    }
                }

                // Проверка на полный платеж
                if (getters.PAYMENTS_TOTAL > 0) {
                    let total = data.cash + data.electronically + getters.PAYMENTS_TOTAL;
                    if (total >= state.estimate.total) {
                        if (fullPayment) {
                            data.payments_method_id = fullPayment.id;
                        }
                    }
                }
            }
            // console.log(data);

            axios
                .post('/admin/payments', data)
                .then(response => {
                    state.payments.push(response.data);
                })
                .catch(error => {
                    console.log(error)
                })
                .finally(() => (state.loading = false));
        },
        REMOVE_PAYMENT({state}, id) {
            state.loading = true;
            const index = state.payments.findIndex(obj => obj.id === id);
            const payment = state.payments[index];

            axios
                .delete('/admin/payments/' + payment.id)
                .then(response => {
                    state.payments.splice(index, 1);
                })
                .catch(error => {
                    console.log(error)
                })
                .finally(() => (state.loading = false));
        },
        CANCEL_PAYMENT({state}, id) {
            state.loading = true;
            const index = state.payments.findIndex(obj => obj.id === id);
            const payment = state.payments[index];

            axios
                .post('/admin/payments/cancel/' + payment.id)
                .then(response => {
                    Vue.set(state.payments, index, response.data);
                })
                .catch(error => {
                    console.log(error)
                })
                .finally(() => (state.loading = false));
        },
    },
    getters: {
        // Смета
        ESTIMATE_AGGREGATIONS: state => {
            let goodsAmount = 0,
                goodsTotal = 0,
                goodsTotalPoints = 0,
                goodsDiscount = 0,
                goodsItemsDiscount = 0;

            if (state.goodsItems.length) {
                state.goodsItems.forEach(item => {
                    goodsAmount += parseFloat(item.amount);
                    goodsTotal += parseFloat(item.total);
                    goodsTotalPoints += parseFloat(item.total_points);
                    goodsDiscount += parseFloat(item.estimate_discount);
                    goodsItemsDiscount += parseFloat(item.discount_currency)
                });
            }

            let servicesAmount = 0,
                servicesTotal = 0,
                servicesTotalPoints = 0,
                servicesDiscount = 0,
                servicesItemsDiscount = 0;

            if (state.servicesItems.length) {
                state.servicesItems.forEach(item => {
                    servicesAmount += parseFloat(item.amount);
                    servicesTotal += parseFloat(item.total);
                    servicesTotalPoints += parseFloat(item.total_points);
                    servicesDiscount += parseFloat(item.estimate_discount);
                    servicesItemsDiscount += parseFloat(item.discount_currency)
                });
            }

            return {
                goods: {
                    amount: goodsAmount.toFixed(2),
                    total: goodsTotal.toFixed(2),
                    totalPoints: goodsTotalPoints.toFixed(2),
                    discount: goodsDiscount.toFixed(2),
                    itemsDiscount: goodsItemsDiscount.toFixed(2),
                },
                services: {
                    amount: servicesAmount.toFixed(2),
                    total: servicesTotal.toFixed(2),
                    totalPoints: servicesTotalPoints.toFixed(2),
                    discount: servicesDiscount.toFixed(2),
                    itemsDiscount: servicesItemsDiscount.toFixed(2),
                },
                estimate: {
                    amount: (goodsAmount + servicesAmount).toFixed(2),
                    total: (goodsTotal + servicesTotal).toFixed(2),
                    totalPoints: (goodsTotalPoints + servicesTotalPoints).toFixed(2),
                    discount: (goodsDiscount + servicesDiscount).toFixed(2),
                    itemsDiscount: (goodsItemsDiscount + servicesItemsDiscount).toFixed(2),
                },
            };
        },

        // Статусы сметы
        IS_REGISTERED: state => {
            return state.estimate.registered_at !== null;
        },
        IS_CONDUCTED: state => {
            return state.estimate.conducted_at !== null;
        },
        IS_DISMISSED: state => {
            return state.estimate.is_dismissed == 1;
        },
        NEED_PRODUCTION: state => {
            return state.needProduction;
        },

        // Товары
        COUNT_GOODS_ITEM_IN_ESTIMATE: state => id => {
            let count = 0;
            state.goodsItems.forEach(item => {
                if (item.price_id == id) {
                    count += parseFloat(item.count);
                }
            });
            return count;
        },
        GOODS_ITEM: state => id => {
            return state.goodsItems.find(item => item.id == id);
        },

        HAS_PRODUCED_GOODS_ITEM: state => {
            const found = state.goodsItems.find(item => item.goods.productions_item !== null);
            return !!found;
        },

        // Услуги
        COUNT_SERVICE_ITEM_IN_ESTIMATE: state => id => {
            let count = 0;
            state.servicesItems.forEach(item => {
                if (item.price_id == id) {
                    count += parseFloat(item.count);
                }
            });
            return count;
        },
        SERVICE_ITEM: state => id => {
            return state.servicesItems.find(item => item.id == id);
        },

        // Платежи
        PAYMENTS: (state, getters) => {
            if (getters.HAS_OUTLET_SETTING('canceled-payments-show')) {
                return state.payments;
            } else {
                return state.payments.filter(payment => payment.canceled_at === null);
            }
        },
        ACTUAL_PAYMENTS: state => {
            return state.payments.filter(payment => payment.canceled_at === null);
        },
        PAYMENTS_TOTAL: state => {
            let total = 0;
            if (state.payments.length) {
                state.payments.forEach(payment => {
                    if (payment.canceled_at == null) {
                        return total += parseFloat(payment.total);
                    }
                });
            }
            return total;
        },

        GET_PAYMENTS_METHOD_ALIAS: state => id => {
            const found = state.paymentsMethods.find(obj => obj.id == id);
            if (found) {
                return found.alias;
            } else {
                return null;
            }
        },
        HAS_OUTLET_SETTING: state => alias => {
            const res = state.outletSettings.find(obj => obj.alias == alias);
            return !!res;
        },
        USER_HAS_OUTLET: state => {
            return state.userOutlets.find(obj => obj.id == state.outlet.id);
        }
    }
};

export default moduleLead;
