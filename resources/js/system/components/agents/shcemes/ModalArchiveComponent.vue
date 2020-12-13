<template>
    <div
        class="reveal rev-small"
        id="modal-scheme-archive"
        data-reveal
    >

        <div class="grid-x">
            <div class="small-12 cell modal-title">
                <h5>Архивирование</h5>
            </div>
        </div>
        <div class="grid-x align-center modal-content ">
            <div class="small-10 cell text-center">
                <p>Архивируем {{ item.name }}, вы уверены?</p>
            </div>
        </div>
        <div class="grid-x align-center grid-padding-x">
            <div class="small-6 medium-4 cell">
                <button
                    @click.prevent="archive"
                    data-close
                    class="button modal-button"
                >Подтвердить</button>
            </div>
            <div class="small-6 medium-4 cell">
                <button
                    data-close class="button modal-button"
                    type="submit"
                >Отменить</button>
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
        archive() {
            axios
                .post('/admin/agency_schemes/archive/' + this.item.id)
                .then(response => {
                    console.log(response.data);
                    if (response.data) {
                        this.$emit('remove', this.item.id);
                        $('#modal-scheme-archive').foundation('close');
                        this.acrchivingScheme = {
                            id: null,
                            name: null
                        };
                    }
                })
                .catch(error => {
                    console.log(error)
                });
        },
    }
}
</script>
