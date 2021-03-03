<template>
    <div
        class="black sprite"
        :class="[{'icon-display-show' : isDisplay == 1}, {'icon-display-hide' : isDisplay == 0}]"
        @click="change"
    >
    </div>
</template>

<script>
export default {
    props: {
        item: Object,
        alias: String
    },
    data() {
        return {
            isDisplay: this.item.display
        }
    },
    computed: {
        curDisplay() {
            return this.isDisplay === 1 ? 0 : 1;
        }
    },
    methods: {
        change() {
            axios
                .post('/admin/display', {
                    id: this.item.id,
                    action: this.curDisplay,
                    entity_alias: this.alias
                })
                .then(response => {
                    if (response.data == true) {
                        this.isDisplay = this.curDisplay;
                    }
                })
                .catch(error => {
                    console.log(error)
                });
        }
    }
}
</script>
