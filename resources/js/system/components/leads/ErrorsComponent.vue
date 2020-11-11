<template>

    <div
        v-if="errors.length"
        class="alert callout"
        data-closable
    >
        <h5>Ошибки:</h5>
        <ul>
            <li v-for="error in errors">{{ error.msg }}</li>
        </ul>
        <button class="close-button" aria-label="Dismiss alert" type="button" data-close>
            <span aria-hidden="true">&times;</span>
        </button>
    </div>

</template>

<script>
export default {
    computed: {
        errors() {
            return this.$store.state.lead.errors;
        },
    }
}
</script>
