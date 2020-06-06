<template>
    <div class="grid-x">
        <div class="small-12 cell radiobutton">Режим<br>

            <input
                type="radio"
                name="mode"
                value="photo"
                id="radiobutton-photo"
                v-model="mode"
            >
            <label for="radiobutton-photo"><span>Фото</span></label>

            <input
                type="radio"
                name="mode"
                value="video"
                id="radiobutton-video"
                v-model="mode"
            >
            <label for="radiobutton-video"><span>Видео</span></label>

        </div>
        <div
            class="grid-x"
            v-show="mode == 'photo'"
        >
            <photo-upload-component
                :options="{title: 'Tiny', name: 'tiny'}"
                :photo='promotion.tiny'
            >
            </photo-upload-component>
            <photo-upload-component
                :options="{title: 'Small', name: 'small'}"
                :photo='promotion.small'
            >
            </photo-upload-component>
            <photo-upload-component
                :options="{title: 'Medium', name: 'medium'}"
                :photo='promotion.medium'
            >
            </photo-upload-component>
            <photo-upload-component
                :options="{title: 'Large', name: 'large'}"
                :photo='promotion.large'
            >
            </photo-upload-component>
            <photo-upload-component
                :options="{title: 'Large X', name: 'large_x'}"
                :photo='promotion.large_x'
            >
            </photo-upload-component>
            <input
                type="hidden"
                name="is_slider"
                value="0"
            >

        </div>

        <div
            class="grid-x"
            v-show="mode == 'video'"
        >
            <div class="cell small-12">
                <label>Горизонтальное
                    <textarea
                        name="horizontal"
                        autocomplete="off"
                        size="10x3"
                    ></textarea>
    <!--                @include('includes.inputs.textarea', ['name' => 'description', 'value' => $promotion->horizontal])-->
                </label>
            </div>
            <div class="cell small-12">
                <label>Вертикальное
                    <textarea
                        name="vertical"
                        autocomplete="off"
                        size="10x3"
                    ></textarea>
    <!--                @include('includes.inputs.textarea', ['name' => 'description', 'value' => $promotion->vertical])-->
                </label>
            </div>
            <div class="cell small-12">
                <label>Квадратное
                    <textarea
                        name="square"
                        autocomplete="off"
                        size="10x3"
                    ></textarea>
    <!--                @include('includes.inputs.textarea', ['name' => 'description', 'value' => $promotion->square])-->
                </label>
            </div>
        </div>

        <div class="small-12 cell checkbox">
            <!--            {!! Form::checkbox('is_slider', 1, $promotion->is_slider, ['id' => 'checkbox-is_slider']) !!}-->
            <input
                type="checkbox"
                name="is_slider"
                value="1"
                id="checkbox-is_slider"
            >
            <label for="checkbox-is_slider"><span>Отображать слайдер</span></label>
        </div>




    </div>
</template>

<script>
    export default {
        props: {
            promotion: Object,
        },
        data() {
            return {
                mode: 'photo',
            }
        },
    }
</script>

<style scoped>

</style>
