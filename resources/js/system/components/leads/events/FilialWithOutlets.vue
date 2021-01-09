<template>
    <div class="grid-x grid-padding-x">

        <div class="cell small-12 large-6">
            <label>Филиал обслуживания
                <select
                    name="filial_id"
                    v-model="filialId"
                    :disabled="isRegistered || canGoodsItems"
                    @change="changeFilial"
                >
                    <option
                        v-for="filial in filials"
                        :value="filial.id"
                    >{{ filial.name }}
                    </option>
                </select>
            </label>
        </div>

        <div class="cell small-12 large-6">
            <label>Торговая точка
                <select
                    name="outlet_id"
                    v-model="outletId"
                    :disabled="isRegistered || canGoodsItems"
                    @change="update"
                >
                    <option
                        v-for="outlet in outletsForFilial"
                        :value="outlet.id"
                      >{{ outlet.name }}
                    </option>
                </select>
            </label>
        </div>

    </div>
</template>

<script>
export default {
    data() {
        return {
            filialId: this.$store.state.lead.lead.filial_id,
            outletId: this.$store.state.lead.lead.outlet_id,
            filials: [],
            outlets: []
        }
    },
    mounted() {
        axios
            .post('/admin/leads/get_user_filials_with_outlets')
            .then(response => {
                if (response.data.filials && response.data.outlets) {
                    this.filials = response.data.filials;
                    this.outlets = response.data.outlets;

                    this.$store.commit('SET_USER_FILIALS', this.filials);
                    this.$store.commit('SET_USER_OUTLETS', this.outlets);
                } else {
                    axios
                        .post('/admin/departments/get_user_filials_with_outlets', {
                            entity: 'leads'
                        })
                        .then(response => {
                            if (response.data.filials && response.data.outlets) {
                                this.filials = response.data.filials;
                                this.outlets = response.data.outlets;
                            } else {
                                this.filials.push(this.$store.state.lead.lead.filial);
                                this.outlets.push(this.$store.state.lead.lead.outlet);
                            }
                        })
                        .catch(error => {
                            alert('Ошибка загрузки, перезагрузите страницу!')
                            console.log(error)
                        });
                }

            })
            .catch(error => {
                alert('Ошибка загрузки, перезагрузите страницу!')
                console.log(error)
            });
    },
    computed: {
        isRegistered() {
            return this.$store.getters.IS_REGISTERED;
        },
        canGoodsItems() {
            return this.$store.state.lead.goodsItems.length > 0;
        },
        outletsForFilial() {
            return this.outlets.filter(item => {
                if (item.filial_id == this.filialId) {
                    return item;
                }
            });
        }
    },
    methods: {
        changeFilial() {
            if (this.outletsForFilial.length) {
                const mainOutlet = this.outletsForFilial.find(outlet => outlet.is_main == 1);
                if (mainOutlet) {
                    this.outletId = mainOutlet.id
                } else {
                    this.outletId = this.outletsForFilial[0].id;
                }
            } else {
                this.outletId = null;
            }

            this.update();
        },
        update() {
            const data = {
                filial_id: this.filialId,
                outlet_id: this.outletId
            };
            this.$store.commit('UPDATE_LEAD_FILIAL', data);

            this.$store.dispatch('GET_OUTLET', this.outletId);
        }
    },


}
</script>
