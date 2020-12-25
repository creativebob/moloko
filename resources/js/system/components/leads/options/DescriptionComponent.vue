<template>
    <labeL>Комментарий
        <textarea
            @input="change"
            v-model="desc"
            :disabled="isConducted"
        >{{ desc }}</textarea>
    </labeL>
</template>

<script>
export default {
    data() {
        return {
            desc: this.$store.state.lead.lead.description
        }
    },
    computed: {
        isConducted() {
            return this.$store.getters.IS_CONDUCTED;
        }
    },
    methods: {
        change() {
            this.$store.commit('UPDATE_LEAD_DESC', this.desc);
        }
    },
}
</script>
