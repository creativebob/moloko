<template>
    <label>{{ title }}
<!--        <input-->
<!--            type="text"-->
<!--            maxlength="5"-->
<!--            autocomplete="off"-->
<!--            pattern="([0-1][0-9]|[2][0-3]):[0-5][0-9]"-->
<!--            placeholder="10:00"-->
<!--            :name="name"-->
<!--            :required="required"-->
<!--        >-->
        <masked-input
            mask="24:59"
            type="text"
            maxlength="5"
            autocomplete="off"
            pattern="([0-1][0-9]|[2][0-3]):[0-5][0-9]"
            placeholder="10:00"
            :name="name"
            :required="required"
        />
    </label>

</template>

<script>
    import MaskedInput from 'vue-masked-input';

    export default {
        components: {
            MaskedInput
        },
        props: {
            name: {
                type: String,
                default: 'time'
            },
            title: {
                type: String,
                default: 'Время'
            },
            value: {
                type: String,
                default: null
            },
            required: {
                type: Boolean,
                default: false
            },
        },
    }
</script>
