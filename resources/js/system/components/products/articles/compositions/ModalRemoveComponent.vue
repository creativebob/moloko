<template>
    <div class="reveal rev-small" :id="'delete-' + name" data-reveal>
        <div class="grid-x">
            <div class="small-12 cell modal-title">
                <h5>Удаление</h5>
            </div>
        </div>
        <div class="grid-x align-center modal-content ">
            <div class="small-10 cell text-center">
                <p>Удаляем "{{ item.article.name }}", вы уверены?</p>
            </div>
        </div>
        <div class="grid-x align-center grid-padding-x">
            <div class="small-6 medium-4 cell">
                <button
                    data-close
                    class="button modal-button"
                    @click="removeItem"
                >Удалить</button>
            </div>
            <div class="small-6 medium-4 cell">
                <button data-close class="button modal-button" id="save-button">Отменить</button>
            </div>
        </div>
        <!--                <div data-close class="icon-close-modal sprite close-modal remove-modal"></div>-->
    </div>
</template>

<script>
    export default {
        props: {
            item: Object,
            name: String
        },
        data() {
            return {
                curItems: this.itemItems,
                text: null,
                search: false,
                error: false,
                results: [],
            }
        },
        methods: {
            removeItem(id) {
                this.$emit('remove', this.item.id);
            }
        }
    }
</script>
