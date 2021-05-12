<template>
    <vue-dropzone ref="myVueDropzone" id="dropzone" :options="dropzoneOptions">
        <slot></slot>
    </vue-dropzone>
</template>

<script>
    import vue2Dropzone from 'vue2-dropzone'
    import 'vue2-dropzone/dist/vue2Dropzone.min.css'

    export default {
        mounted() {
            this.dropzoneOptions.acceptedFiles = this.settings.img_formats;
            this.dropzoneOptions.init = function() {
                this.on("success", function(file, responseText) {
                    file.previewTemplate.setAttribute('id',responseText[0].id);


                    $.post('/admin/photo_store', {
                        id: this.itemId,
                        entity: this.entity,
                    }, function(html){
                        // alert(html);
                        $('#photos-list').html(html);
                    })
                });
                this.on("thumbnail", function(file) {

                    if (file.width < this.settings.img_min_width || file.height < this.settings.img_min_height) {
                        // file.rejectDimensions();
                    } else {
                        // file.acceptDimensions();
                    }
                });
            };
            // accept: function(file, done) {
            //     file.acceptDimensions = done;
            //     file.rejectDimensions = function() { done("Размер фото мал, нужно минимум px в ширину"); };
            // },
        },
        props: {
            itemId: Number,
            entity: String,
            settings: Object
        },
        methods: {
            // sendingEvent (file, xhr, formData) {
            //     formData.append('maxFilesize', this.dropzone.img_max_size);
            // },

            // someMethod: {
            //     this.$refs.myVueDropzone.setOption('maxFilesize', this.dropzone.img_max_size)
            // }
        },
        components: {
            vueDropzone: vue2Dropzone
        },
        data() {
            return {
                options: {},
                dropzoneOptions: {
                    url: '/admin/photo_store',
                    headers: {
                        "My-Awesome-Header": "header value"
                    },
                    paramName: 'photo',
                    maxFiles: 20,
                    // acceptedFiles: this.dropzone.img_formats,
                    addRemoveLinks: true,
                    dictDefaultMessage: 'Перетащите файлы в область или кликните по ней',

                }
            }
        }
    }
</script>
