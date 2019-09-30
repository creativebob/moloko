<template>
    <vue-dropzone ref="myVueDropzone" id="dropzone" :options="dropzoneOptions"></vue-dropzone>
</template>

<script>

    import vue2Dropzone from 'vue2-dropzone'
    import 'vue2-dropzone/dist/vue2Dropzone.min.css'

    export default {
        mounted() {
            console.log('DropzoneComponent mounted.');
            // alert(this.dropzone.img_max_size);
            // alert(this.$refs.myVueDropzone);
            // setTimeout(() => {
            //     this.$refs.myVueDropzone.dropzone.options.maxFilesize = this.dropzone.img_max_size
            // }, 0);
        },
        props: [
            'dropzone'
        ],
        methods: {
            // sendingEvent (file, xhr, formData) {
            //     formData.append('maxFilesize', this.dropzone.img_max_size);
            // },

            // someMethod: {
            //     this.$refs.myVueDropzone.setOption('maxFilesize', this.dropzone.img_max_size)
            // }
        },
        name: 'vue2Dropzone',
        components: {
            vueDropzone: vue2Dropzone
        },
        data: function () {
            return {
                dropzoneOptions: {
                    url: '/api/v1/dropzone',
                    headers: {
                        "My-Awesome-Header": "header value"
                    },
                    paramName: 'photo',
                    maxFilesize: this.dropzone.img_max_size,
                    maxFiles: 20,
                    // acceptedFiles: this.dropzone.img_formats,
                    addRemoveLinks: true,
                    dictDefaultMessage: 'Перетащите файлы в область или кликните по ней',
                    // init: function() {
                    //     this.on("success", function(file, responseText) {
                    //         file.previewTemplate.setAttribute('id',responseText[0].id);
                    //
                    //         $.post('/api/v1/dropzone', {
                    //             id: this.item_id,
                    //             entity: this.item_entity,
                    //         }, function(html){
                    //             // alert(html);
                    //             $('#photos-list').html(html);
                    //         })
                    //     });
                    //     this.on("thumbnail", function(file) {
                    //
                    //         if (file.width < this.dropzone.img_min_width || file.height < this.dropzone.img_min_height) {
                    //             file.rejectDimensions();
                    //         } else {
                    //             file.acceptDimensions();
                    //         }
                    //     });
                    // },
                    // accept: function(file, done) {
                    //     file.acceptDimensions = done;
                    //     file.rejectDimensions = function() { done("Размер фото мал, нужно минимум px в ширину"); };
                    // },
                }
            }
        }
    }
</script>