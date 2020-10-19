const moduleLead = {
        state: {
            users: [],
            companies: [],

            lead: null,
            client: null,
            estimate: null,
            goodsItems: [],
            servicesItems: [],

            change: false,
            loading: false,
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

            // Смета
            SET_ESTIMATE(state, estimate) {
                state.estimate = estimate;
            },
            UPDATE_ESTIMATE(state) {
                // var amount = 0;
                // if (state.goodsItems.length > 0) {
                //     var goodsItemsAmount = 0;
                //     state.goodsItems.forEach(function(item) {
                //         return goodsItemsAmount += Number(item.amount)
                //     });
                //     state.goodsItems.amount = goodsItemsAmount;
                //     amount += goodsItemsAmount;
                // }
                // if (state.servicesItems.length > 0) {
                //     var servicesItemsAmount = 0;
                //     state.servicesItems.forEach(function(item) {
                //         return servicesItemsAmount += Number(item.amount)
                //     });
                //     state.servicesItems.amount = servicesItemsAmount
                //     amount += servicesItemsAmount;
                // }
                // state.estimate.amount = amount;
                //
                // let total = 0;
                // if (amount > 0) {
                //     let discountAmount = (amount * state.estimate.discount_percent) / 100;
                //
                //     total += amount - discountAmount;
                // }
                // state.estimate.total = total;
            },

            // Товары
            SET_GOODS_ITEMS(state, goodsItems) {
                state.goodsItems = goodsItems;
            },

            ADD_GOODS_ITEM_TO_ESTIMATE(state, price) {
                if (!state.estimate.registered_at) {

                    // TODO - 25.09.20 - Нужна будет проверка на серийность
                    let index = state.goodsItems.findIndex(obj => obj.price_id == price.id);
                    if (index > -1) {
                        let item = state.goodsItems[index];
                        item.count = parseFloat(item.count) + 1;
                        Vue.set(state.goodsItems, index, item);

                        this.commit('SET_AGGREGATIONS', index);
                    } else {
                        let item = {
                            id: state.goodsItems.length + 1,

                            estimate_id: state.estimate.id,
                            price_id: price.id,

                            goods_id: price.goods_id,
                            goods: price.goods,

                            currency_id: price.currency_id,
                            currency: price.currency,

                            sale_mode: 1,
                            comment: null,

                            cost_unit: parseFloat(price.goods.article.cost_default),
                            price: parseFloat(price.price),
                            points: price.points,
                            count: 1,

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
                        state.goodsItems.push(item);
                        index = state.goodsItems.findIndex(obj => obj.id == item.id);

                        this.commit('SET_AGGREGATIONS', index);
                    }
                }
            },
            UPDATE_GOODS_ITEM(state, item) {
                const index = state.goodsItems.findIndex(obj => obj.id === item.id);
                Vue.set(state.goodsItems, index, item);

                this.commit('SET_AGGREGATIONS', index);
            },

            SET_AGGREGATIONS(state, index) {
                this.commit('SET_CHANGE');
                let item = state.goodsItems[index];
                const count = item.count;

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
                        let totalPrice = parseFloat(item.price) - item.price_discount_unit - item.catalogs_item_discount_unit - item.estimate_discount_unit - item.client_discount_unit_currency;
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
                state.goodsItems.splice(index, 1);
                this.commit('SET_CHANGE');
            },
            UPDATE_GOODS_ITEMS(state) {
                state.goodsItems.forEach(item => {
                    const index = state.goodsItems.findIndex(obj => obj.id === item.id);
                    this.commit('SET_AGGREGATIONS', index);
                });
            },

            // Услуги
            SET_SERVICES_ITEMS(state, servicesItems) {
                state.servicesItems = servicesItems;
            },
            UPDATE_SERVICES_ITEM(state, item) {
                let id = item.id;
                let index = state.servicesItems.findIndex(item => item.id === id);
                Vue.set(state.servicesItems, index, item);

                this.commit('UPDATE_ESTIMATE');
            },
            UPDATE_SERVICES_ITEMS(state, servicesItems) {
                state.servicesItems = servicesItems;

                this.commit('UPDATE_ESTIMATE');
            },

            // Изменения
            SET_CHANGE(state) {
                state.change = true;
            },

            // Платежи
            ADD_PAYMENT(state, payment) {
                state.estimate.payments.push(payment);
            }
        },
        actions: {
            // Обновление лида и сметы
            UPDATE({state}, data) {
                state.loading = true;
                axios
                    .patch('/admin/leads/axios_update/' + state.lead.id, data)
                    .then(response => {
                        // console.log(response.data);

                        const lead = response.data.lead;
                        this.commit('SET_LEAD', lead);
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

                        this.commit('SET_ESTIMATE', response.data.estimate);

                        this.commit('SET_GOODS_ITEMS', response.data.goods_items);

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
            UNRESERVE_ESTIMATE({state}) {
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
            UNRESERVE_GOODS_ITEM({state}, id) {
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
                    .patch('/admin/estimates/' + state.estimate.id + '/unregistering')
                    .then(response => {
                        this.commit('SET_ESTIMATE', response.data);
                    })
                    .catch(error => {
                        console.log(error)
                    })
                    .finally(() => (state.loading = false));
            },

            // Продажа сметы
            SALE_ESTIMATE({state}) {
                state.loading = true;
                axios
                    .patch('/admin/estimates/' + state.estimate.id + '/saling/')
                    .then(response => {
                        // console.log(response.data);
                        this.commit('SET_ESTIMATE', response.data);
                    })
                    .catch(error => {
                        console.log(error)
                    })
                    .finally(() => (state.loading = false));
            },

            // Товары

            // ADD_GOODS_ITEM_TO_ESTIMATE({ state }, priceId) {
            //     if (state.estimate.is_registered === 0) {
            //
            //         axios
            //             .post('/admin/estimates_goods_items', {
            //                 estimate_id: state.estimate.id,
            //                 price_id: priceId,
            //                 client_discount_percent: state.client ? state.client.discount : 0,
            //             })
            //             .then(response => {
            //                 if (response.data.success) {
            //                     let item = response.data.item,
            //                         index = state.goodsItems.findIndex(obj => obj.id === item.id);
            //
            //                     if (index > -1) {
            //                         Vue.set(state.goodsItems, index, item);
            //                     } else {
            //                         state.goodsItems.push(item);
            //                     }
            //
            //                     this.commit('UPDATE_ESTIMATE');
            //                 } else {
            //                     alert('Невозможно добавить позицию, так как цена изменилась. Удалите позицию со старой ценой и добавьте позицию заново');
            //                 }
            //
            //             })
            //             .catch(error => {
            //                 console.log(error)
            //             });
            //     }
            // },
            // REMOVE_GOODS_ITEM_FROM_ESTIMATE({state}, itemId) {
            //     if (state.estimate.is_registered === 0) {
            //         axios
            //             .delete('/admin/estimates_goods_items/' + itemId)
            //             .then(response => {
            //                 if (response.data > 0) {
            //
            //                     let index = state.goodsItems.findIndex(obj => obj.id === itemId);
            //                     state.goodsItems.splice(index, 1);
            //
            //                     this.commit('UPDATE_ESTIMATE');
            //                 }
            //             })
            //             .catch(error => {
            //                 console.log(error)
            //             });
            //     }
            // },

            // Услуги
            ADD_SERVICES_ITEM_TO_ESTIMATE({state}, priceId) {
                if (state.estimate.registered_at) {
                    axios
                        .post('/admin/estimates_services_items', {
                            estimate_id: state.estimate.id,
                            price_id: priceId,
                        })
                        .then(response => {
                            if (response.data.success) {
                                let item = response.data.item,
                                    index = state.servicesItems.findIndex(obj => obj.id === item.id);

                                if (index > -1) {
                                    Vue.set(state.servicesItems, index, item);
                                } else {
                                    state.servicesItems.push(item);
                                }

                                this.commit('UPDATE_ESTIMATE');
                            } else {
                                alert('Невозможно добавить позицию, так как цена изменилась. Удалите позицию со старой ценой и добавьте позицию заново');
                            }

                        })
                        .catch(error => {
                            console.log(error)
                        });
                }
            },
            REMOVE_SERVICES_ITEM_FROM_ESTIMATE({state}, itemId) {
                if (state.estimate.registered_at) {
                    axios
                        .delete('/admin/estimates_services_items/' + itemId)
                        .then(response => {
                            if (response.data > 0) {
                                let index = state.servicesItems.findIndex(obj => obj.id === itemId);
                                state.servicesItems.splice(index, 1);

                                this.commit('UPDATE_ESTIMATE');
                            }
                        })
                        .catch(error => {
                            console.log(error)
                        });
                }
            }
            ,
        },
        getters: {
            // Смета
            estimateAmount: state => {
                let goodsAmount = 0,
                    servicesAmount = 0;

                if (state.goodsItems.length) {
                    state.goodsItems.forEach(item => {
                        return goodsAmount += parseFloat(item.amount)
                    });
                }

                if (state.servicesItems.length) {
                    state.servicesItems.forEach(item => {
                        return servicesAmount += parseFloat(item.amount)
                    });
                }

                let amount = goodsAmount + servicesAmount;
                return amount.toFixed(2);
            },
            estimateTotalPoints: state => {
                let goodsTotalPoints = 0,
                    servicesTotalPoints = 0;

                if (state.goodsItems.length) {
                    state.goodsItems.forEach(item => {
                        return goodsTotalPoints += parseFloat(item.total_points)
                    });
                }

                if (state.servicesItems.length) {
                    state.servicesItems.forEach(item => {
                        return servicesTotalPoints += parseFloat(item.total_points)
                    });
                }

                let totalPoints = goodsTotalPoints + servicesTotalPoints;
                return totalPoints.toFixed(2);
            },
            estimateItemsDiscount: state => {
                let goodsDiscount = 0,
                    servicesDiscount = 0;

                if (state.goodsItems.length) {
                    state.goodsItems.forEach(item => {
                        return goodsDiscount += parseFloat(item.discount_currency)
                    });
                }

                if (state.servicesItems.length) {
                    state.servicesItems.forEach(item => {
                        return servicesDiscount += parseFloat(item.discount_currency)
                    });
                }

                let discount = goodsDiscount + servicesDiscount;
                return discount.toFixed(2);
            },
            estimateDiscount: state => {
                let goodsDiscount = 0,
                    servicesDiscount = 0;

                if (state.goodsItems.length) {
                    state.goodsItems.forEach(item => {
                        if (item.is_manual == 0) {
                            goodsDiscount += parseFloat(item.estimate_discount);
                        }

                    });
                }

                if (state.servicesItems.length) {
                    state.servicesItems.forEach(item => {
                        if (item.is_manual == 0) {
                            return servicesDiscount += parseFloat(item.estimate_discount);
                        }
                    });
                }

                let discount = goodsDiscount + servicesDiscount;
                return discount.toFixed(2);
            },
            estimateTotal: state => {
                let total = 0;

                if (state.goodsItems.length) {
                    state.goodsItems.forEach(item => {
                        return total += parseFloat(item.total)
                    });
                }

                if (state.servicesItems.length) {
                    state.servicesItems.forEach(item => {
                        return total += parseFloat(item.total)
                    });
                }
                return parseFloat(total);
            },

            // Товары
            countGoodsItemInEstimate: state => id => {
                let count = 0;
                state.goodsItems.forEach(item => {
                    if (item.price_id == id) {
                        count += parseFloat(item.count);
                    }
                });
                return count;
            },

            // Клиент
            clientDiscountPercent: state => {
                return state.client ? state.client.discount : 0;
            },

            // Платежи
            paymentsAmount: state => {
                let amount = 0;
                if (state.estimate.payments.length) {
                    state.estimate.payments.forEach(function (item) {
                        return amount += parseFloat(item.amount)
                    });
                }
                return amount;
            }

        }
    }
;

export default moduleLead;
