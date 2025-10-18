<template>
    <v-expansion-panels v-model="panel" class="builder-item">
        <v-expansion-panel>
            <v-expansion-panel-header>
                <div class="d-flex align-center">
                    <img
                        class="builder-item__header-image mr-3"
                        src="/storage/images/gallery.png"
                    />
                    <b>Галерея</b>
                </div>
            </v-expansion-panel-header>
                <v-expansion-panel-content>
                    <el-upload
                        ref="fileUpload"
                        action="https://jsonplaceholder.typicode.com/posts/"
                        list-type="picture-card"
                        :on-change="updateImageList"
                        :on-preview="handleImagePreview"
                        :on-remove="handleImageRemove"
                        :auto-upload="false"
                        accept="image/jpeg, image/png"
                        class="damage-form__file-upload"
                    >
                        <i class="el-icon-plus" />
                    </el-upload>
                    <el-dialog :visible.sync="dialogVisible">
                        <img width="100%" :src="dialogImageUrl" alt>
                    </el-dialog>
                </v-expansion-panel-content>
        </v-expansion-panel>
    </v-expansion-panels>
</template>

<script>
export default {
    name: 'VirtualTourContentBuilderSlider',

    props: {

    },

    data() {
        return {
            panel: [],
            images: [],
            dialogImageUrl: '',
            dialogVisible: false,
        }
    },

    methods: {
        updateImageList(file) {
            this.images.push(file.raw);
        },

        handleImagePreview(file) {
            this.dialogImageUrl = file.url;
            this.dialogVisible = true;
        },

        handleImageRemove(file) {
            this.images = this.images.filter((item) => item.uid === file.uid);
        },
    }
}
</script>

<style lang="scss">

</style>
