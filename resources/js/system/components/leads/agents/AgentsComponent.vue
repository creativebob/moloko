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
                v-if="!isConducted"

                class="cell small-12 input-group"
            >
                <div
                    v-if="agents.length"
                    class="input-group-field"
                >
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
    props: {
        catalogId: {
            type: Number,
            default: null
        },
    },
    data() {
        return {
            agents: [],
            agentId: null
        }
    },
    computed: {
        actualCatalogId() {
            return this.$store.state.lead.catalogGoodsId;
        },
        agent() {
            return this.$store.state.lead.agent;
        },
        estimate() {
            return this.$store.state.lead.estimate;
        },
        isConducted() {
            return this.$store.state.lead.estimate.conducted_at !== null;
        },
    },
    watch: {
        agentId() {
            if (this.agent) {
                const agent = this.agents.find(obj => obj.id == this.agentId);
                this.$store.commit('SET_AGENT', agent);
            }
        },
        actualCatalogId() {
            this.getAgents(this.actualCatalogId);
        },
    },
    mounted() {
        if (this.catalogId) {
            this.getAgents(this.catalogId);
        }
    },
    methods: {
        getAgents(catalogId) {
            if (!this.agent) {
                axios
                    .get('/admin/agents/get-agents-by-catalog-goods-id/' + catalogId)
                    .then(response => {
                        this.agents = response.data;
                        this.agentId = this.agents.length ? this.agents[0].id : null;
                    })
                    .catch(error => {
                        console.log(error)
                    });
            }
        },
        change() {

        },
        update() {
            this.getAgents(this.catalogId);
        },
        set() {
            const agent = this.agents.find(obj => obj.id == this.agentId);
            this.$store.commit('SET_AGENT', agent);
            this.$store.dispatch('SET_AGENT_FOR_ESTIMATE');

        }
    }
}
</script>
