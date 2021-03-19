<template>
    <fieldset class="fieldset-access">
        <legend>Дополнительные SEO</legend>
        <div class="grid-x">
            <div class="cell small-12">
                <a
                    class="button"
                    data-open="modal-add-additional-seo"
                >Добавить SEO</a>
            </div>

            <div class="cell small-12">
                <table class="table-compositions">
                    <thead>
                    <tr>
                        <th>Title</th>
                        <th>H1</th>
                        <th></th>
                        <th></th>
                    </tr>
                    </thead>
                        <seo-component
                            v-for="(seo, index) in seos"
                            :seo="seo"
                            :key="index"
                            :index="index"
                            :columns="columns"
                        ></seo-component>
                </table>
            </div>

            <modal-store-component
                :columns="columns"
            ></modal-store-component>

            <modal-update-component
                :columns="columns"
            ></modal-update-component>

            <modal-delete-component></modal-delete-component>
        </div>
    </fieldset>

</template>

<script>

export default {
    components: {
        'modal-store-component': require('./modals/StoreComponent'),
        'seo-component': require('./SeoComponent'),
        'modal-update-component': require('./modals/UpdateComponent'),
        'modal-delete-component': require('./modals/DeleteComponent'),
    },
    data() {
        return {
            columns: [
                'title',
                'h1',
                'description',
                'keywords',
                'content',
                'is_canonical',

                'params'
            ],
        }
    },
    computed: {
        seos() {
            return this.$store.state.seo.additionals;
        },
    }
}
</script>
