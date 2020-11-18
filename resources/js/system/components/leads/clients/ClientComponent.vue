<template>
    <div
        v-if="client"
        class="grid-x grid-padding-x"
    >
        <div class="cell small-12">

            <div
                v-if="client.clientable_type === 'App\\User'"
                class="grid-x"
            >
                <div class="cell small-12">
                    {{ client.clientable.name }}
                </div>
            </div>

            <div
                v-if="client.clientable_type === 'App\\Company'"
                class="grid-x"
            >
                <div class="cell small-12">
                    {{ client.clientable.name }}
                </div>
            </div>

            <div class="grid-x">
                <div class="cell small-12">
                    Скидка: {{ client.discount }}
                </div>
            </div>

        </div>
    </div>
</template>

<script>
export default {
    computed: {
        client() {
            return this.$store.state.lead.client;
        }
    }
}
</script>
