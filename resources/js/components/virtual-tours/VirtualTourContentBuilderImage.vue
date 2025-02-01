<template>
    <v-expansion-panels v-model="panel" class="builder-item">
        <v-expansion-panel>
            <v-expansion-panel-header>
                <div class="d-flex align-center">
                    <img
                        class="builder-item__header-image mr-3"
                        src="/storage/images/image.png"
                    />
                    <b>Зображення</b>
                </div>
            </v-expansion-panel-header>
                <v-expansion-panel-content>
                    <el-upload
                        ref="fileUpload"
                        class="damage-form__drag-upload"
                        drag
                        action="https://jsonplaceholder.typicode.com/posts/"
                        :on-change="updateImageList"
                        :on-remove="handleImageRemove"
                        :auto-upload="false"
                        :limit="1"
                        accept="image/jpeg, image/png"
                    >
                        <i class="el-icon-upload"></i>
                        <div class="el-upload__text">Перетягніть зображення чи <em>натисніть сюди</em></div>
                        <div class="el-upload__tip mt-0" slot="tip">Додайте зображення туру у <b>jpg</b> чи <b>png</b> форматі</div>
                    </el-upload>
                </v-expansion-panel-content>
        </v-expansion-panel>
    </v-expansion-panels>
</template>

<script>
export default {
    name: 'VirtualTourContentBuilderImage',

    props: {

    },

    data() {
        return {
            panel: [],
            images: [],
        }
    },

    methods: {
        updateImageList(file) {
            this.images = [file.raw];
        },

        handleImageRemove() {
            this.images = [];
        },
    }
}
</script>

<style lang="scss">

</style>
