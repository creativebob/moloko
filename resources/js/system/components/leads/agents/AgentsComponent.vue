<template>
    <div class="grid-x grid-padding-x">
        <div
            v-if="agent"
            class="cell small-12"
        >
            Агент: {{ agent.company.name }}<br>
            Вознаграждение агента: {{ estimate.share_currency }}<br>
            Наша сумма: {{ estimate.principal_currency }}<br>
        </div>

        <template v-else>
            <div
                v-if="!isConducted && !isDismissed && agents.length"

                class="cell small-12 input-group"
            >
                <div class="input-group-field">
                    <select v-model="agentId">
                        <option
                            v-for="agent in agents"
                            :value="agent.id"
                        >{{ agent.company.name }}
                        </option>
                    </select>
                </div>
                <div class="input-group-button">
                    <a
                        @click="set"
                        class="button"
                    >Передать агенту</a>
                </div>
            </div>
        </template>
    </div>
</template>

<script>
export default {
    // props: {
    //     catalogGoodsId: {
    //         type: Number,
    //         default: null
    //     },
    //     catalogServicesId: {
    //         type: Number,
    //         default: null
    //     },
    // },
    data() {
        return {
            agents: [],
            agentId: null
        }
    },
    computed: {
        actualCatalogGoodsId() {
            return this.$store.state.lead.catalogGoodsId;
        },
        actualCatalogServicesId() {
            return this.$store.state.lead.catalogServicesId;
        },
        agent() {
            return this.$store.state.lead.agent;
        },
        estimate() {
            return this.$store.state.lead.estimate;
        },
        isConducted() {
            return this.$store.getters.IS_CONDUCTED;
        },
        isDismissed() {
            return this.$store.getters.IS_DISMISSED;
        }
    },
    watch: {
        agentId() {
            if (this.agent) {
                const agent = this.agents.find(obj => obj.id == this.agentId);
                this.$store.commit('SET_AGENT', agent);
            }
        },
        actualCatalogGoodsId() {
            this.getAgents();
        },
        // actualCatalogServicesId() {
        //     this.getAgents();
        // },
    },
    // mounted() {
    //     this.getAgents();
    // },
    methods: {
        getAgents() {
            if (!this.agent && (this.actualCatalogGoodsId || this.actualCatalogServicesId)) {
                axios
                    .post('/admin/agents/get_agents_by_catalogs_ids', {
                        catalog_goods_id: this.actualCatalogGoodsId,
                        catalog_services_id: this.actualCatalogServicesId,
                    })
                    .then(response => {

                        this.agents = response.data;
                        this.$store.commit('SET_COUNT_AGENTS', this.agents.length);
                        this.agentId = this.agents.length ? this.agents[0].id : null;
                    })
                    .catch(error => {
                        console.log(error)
                    });
            }
        },
        update() {
            this.getAgents();
        },
        set() {
            const agent = this.agents.find(obj => obj.id == this.agentId);
            this.$store.commit('SET_AGENT', agent);
            this.$store.dispatch('SET_AGENT_FOR_ESTIMATE');

        }
    }
}
</script>
