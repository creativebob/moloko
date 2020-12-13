<template>
    <div class="grid-x grid-padding-x">
        <div class="cell medium-3">
            <label>Имя схемы
                <string-component
                    v-model="name"
                    ref="nameComponent"
                ></string-component>
            </label>
        </div>
        <div class="cell medium-6">
            <label>Описание
                <textarea-component
                    v-model="description"
                    ref="descriptionComponent"
                ></textarea-component>
            </label>
        </div>
        <div class="cell medium-3">
            <label>Доля агента (%)
                <div class="input-group">
                    <digit-component
                        v-model="percent"
                        ref="percentComponent"
                        :limit-max="100"
                    ></digit-component>
                    <div class="input-group-button">
                        <a
                            @click="add"
                            class="button"
                        >+</a>
                    </div>
                </div>
            </label>
            <!--                <span v-if="error">Такой прайс существует!</span>-->
        </div>
    </div>
</template>

<script>

export default {
    components: {
        'string-component': require('../inputs/StringComponent'),
        'textarea-component': require('../inputs/TextareaComponent'),
        'digit-component': require('../inputs/DigitComponent'),
    },
    props: {
        catalogId: Number,
        alias: String
    },
    data() {
        return {
            name: null,
            description: null,
            percent: 0,
        }
    },
    methods: {
        add() {
            if (this.percent > 0 && (this.name || this.name.length)) {
                const data = {
                    catalog_id: this.catalogId,
                    catalog_type: this.alias == 'catalogs_goods' ? 'App\\CatalogsGoods' : 'App\\CatalogService',

                    name: this.name,
                    description: this.description,
                    percent_default: this.percent,
                };

                axios
                    .post('/admin/agency_schemes', data)
                    .then(response => {
                        this.$emit('add', response.data);
                        this.reset();
                    })
                    .catch(error => {
                        console.log(error)
                    });

            }
        },
        reset() {
            this.name = null;
            this.$refs.nameComponent.update();

            this.description = null;
            this.$refs.descriptionComponent.update();

            this.percent = 0;
            this.$refs.percentComponent.update();
        },
    },
    directives: {
        focus: {
            inserted: function (el) {
                el.focus()
            }
        }
    },
}
</script>
