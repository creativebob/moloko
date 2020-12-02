<template>
    <div class="reveal rev-small" id="modal-role-remove" data-reveal>
        <div class="grid-x">
            <div class="small-12 cell modal-title">
                <h5>удаление</h5>
            </div>
        </div>
        <div
            v-if="item"
            class="grid-x align-center modal-content"
        >
            <div class="small-10 cell text-center">
                <p>Удаляем роль - {{ item.role.name }}, вы уверены?</p>
            </div>
        </div>
        <div class="grid-x align-center grid-padding-x">
            <div class="cell small-6 medium-4">
                <button
                    data-close
                    class="button modal-button"
                    @click="remove"
                >Удалить</button>
            </div>
            <div class="cell small-6 medium-4">
                <button data-close class="button modal-button">Отмена</button>
            </div>
        </div>
    </div>

</template>

<script>
export default {
    props: {
        item: Object
    },
    methods: {
        remove() {
            this.$emit('remove', this.item)
        }
    }

}
</script>
