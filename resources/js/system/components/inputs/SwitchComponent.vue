<template>
    <div class="switch tiny component">
        <input 
            type="hidden"
             :name="name"
             value=0
        >
        <input 
            class="switch-input"
            :class="classes"
            :id="id"
            type="checkbox"
            :name="name"
            :disabled="disabled"
            v-on:change="$emit('change', $event.target.checked)"
            v-bind:checked="checked"
            :value="1"
        >
        <label 
            class="switch-paddle" 
            :for="id">
            <span class="show-for-sr">{{ title }}</span>
        </label>
    </div>

</template>

<script>
    export default {

        model: {
            prop: 'checked',
            event: 'change'
        },

        props: {
            name: {
                type: String,
                default: 'name'
            },
            checked: {
                type: Boolean,
                default: false
            },
            title: {
                type: String,
                default: ''
            },
            id: {
                type: String,
                default: null
            },
            classes: {
                type: String,
                default: null
            },
            disabled: {
                type: Boolean,
                default: false
            },
        },
    }
</script>
