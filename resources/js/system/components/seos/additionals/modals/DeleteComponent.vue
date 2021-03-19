<template>
    <div
        class="reveal"
        id="modal-delete-additional-seo"
        data-reveal
        data-close-on-click="false"
    >
        <div class="grid-x">
            <div class="cell small-12 modal-title">
                <h5>Удаление SEO</h5>
            </div>
        </div>
        <div class="grid-x align-center modal-content ">
            <div class="small-10 cell text-center">
                <p>Удаляем SEO "<span class="title-price">{{ item.title }}</span>", вы уверены?</p>
            </div>
        </div>
        <div class="grid-x align-center grid-padding-x">
            <div class="small-6 medium-4 cell">
                <button
                    @click.prevent="remove"
                    data-close
                    class="button modal-button"
                >Удалить</button>
            </div>
            <div class="small-6 medium-4 cell">
                <button data-close class="button modal-button" id="save-button" type="submit">Отменить</button>
            </div>
        </div>
        <div data-close class="icon-close-modal sprite close-modal remove-modal"></div>
    </div>
</template>

<script>

export default {
    data() {
        return {
            title: null,
        }
    },
    computed: {
        item() {
            return this.$store.state.seo.deletingSeo;
        }
    },
    watch: {
        item(item) {
            this.title = item.title;
        }
    },
    methods: {
        remove() {
            $('#modal-delete-additional-seo').foundation('close');
            this.$store.commit('DELETE_SEO');
        },
    },
}
</script>
