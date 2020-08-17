<template>
    <div>
        <input
            type="checkbox"
            :name="name + '[]'"
            :id="'checkbox-' + name + '-' + item.id"
            :value="item.id"
            :checked="checkChecked"
            @change="change($event.target.checked)"
        >

        <label :for="'checkbox-' + name + '-' + item.id">
            <span>{{ item.name }}</span>
        </label>
    </div>

</template>

<script>
    export default {
        props: {
            item: Object,
            startChecked: Boolean,
            name: String
        },
        computed: {
            checkChecked() {
                return this.startChecked;
            }
        },
        methods: {
            change(checked) {
                if (checked) {
                    this.$emit('add', this.item);
                } else {
                    this.$emit('remove', this.item.id);
                }
            }
        }
    }
</script>
