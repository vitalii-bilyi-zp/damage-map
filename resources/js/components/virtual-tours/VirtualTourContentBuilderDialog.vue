<template>
    <v-dialog
        v-model="dialog"
        :max-width="options.width"
        :style="{ zIndex: options.zIndex }"
        class="builder-dialog"
        @keydown.esc="cancel"
    >
        <v-card>
            <v-card-title class="text-h5 grey lighten-2 px-4">
                Додати елемент
            </v-card-title>

            <template>
                <v-card-text class="pa-4">
                    <v-row>
                        <v-col
                            v-for="item in builderItems"
                            :key="item.id"
                            cols="12"
                            sm="6"
                        >
                            <v-card
                                class="builder-dialog__card pa-2"
                                :class="{'builder-dialog__card--selected': builderItem && builderItem.id === item.id}"
                                outlined
                                tile
                                :disabled="item.disabled"
                                @click="selectItem(item)"
                            >
                                <div class="text-center">
                                    <img
                                        class="builder-dialog__card-image"
                                        :class="item.imageClass"
                                        :src="`/storage/images/${item.image}`"
                                    />
                                </div>

                                <v-card-title class="builder-dialog__card-text">
                                    {{ item.name }}
                                </v-card-title>
                            </v-card>
                        </v-col>
                    </v-row>
                </v-card-text>

                <v-divider></v-divider>
            </template>

            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn
                    v-if="!options.noconfirm"
                    color="grey"
                    text
                    class="body-2 font-weight-bold"
                    @click.native="cancel"
                >
                    Закрити
                </v-btn>
                <v-btn
                    color="primary"
                    class="body-2 font-weight-bold ml-4"
                    outlined
                    :disabled="!builderItem"
                    @click.native="confirm"
                >
                    Підтвердити
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
export default {
    name: 'VirtualTourContentBuilderDialog',

    props: {

    },

    data() {
        return {
            dialog: false,
            options: {
                width: 600,
                zIndex: 200,
                noconfirm: false
            },
            builderItem: null,
            builderItems: [
                {
                    id: 'text',
                    name: 'Текст',
                    image: 'text.png',
                    imageClass: 'builder-dialog__card-image--text'
                },
                {
                    id: 'image',
                    name: 'Зображення',
                    image: 'image.png',
                    imageClass: 'builder-dialog__card-image--image'
                },
                {
                    id: 'slider',
                    name: 'Галерея',
                    image: 'gallery.png',
                    imageClass: 'builder-dialog__card-image--slider'
                },
                {
                    id: '3d',
                    name: '3D Панорама',
                    image: '3d-camera.png',
                    imageClass: 'builder-dialog__card-image--3d',
                    disabled: true,
                }
            ],
        }
    },

    methods: {
        open(options) {
            this.dialog = true;
            this.options = Object.assign(this.options, options);
        },
        selectItem(item) {
            if (item.disabled) {
                return;
            }

            this.builderItem = item;
        },
        confirm() {
            this.$emit('select-item', this.builderItem.id);
            this.builderItem = null;
            this.dialog = false;
        },
        cancel() {
            if (this.options.noconfirm) {
                return;
            }

            this.dialog = false;
        },
    }
}
</script>

<style lang="scss" scoped>
.builder-dialog__card {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    height: 190px;
    border-radius: 6px !important;
    border: 1px dashed #d9d9d9;

    &:hover {
        border-color: #1976d2;
    }

    &--selected {
        border-color: #1976d2;

        &:before {
            background-color: #1976d2;
            opacity: .08;
        }
    }
}

.builder-dialog__card-image {
    height: 100px;
    width: auto;

    &--text {

    }

    &--image {
        height: 90px;
        margin-top: 10px;
    }

    &--slider {

    }

    &--3d {
        height: 90px;
        margin-top: 8px;
    }
}

.builder-dialog__card-text {
    justify-content: center;
}
</style>

