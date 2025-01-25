<template>
    <div class="damage-form">
        <v-form>
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
                <div class="el-upload__tip mt-0" slot="tip">Оберіть зображення туру у <b>jpg</b> чи <b>png</b> форматі</div>
            </el-upload>

            <v-divider class="my-5"/>

            <v-autocomplete
                v-model="damageNoteId"
                :items="damageNoteOptions"
                :loading="isSearching"
                :search-input.sync="searchQuery"
                :filter="damageNotesFilter"
                label="Пошкоджений об'єкт"
                dense
                outlined
                item-text="address"
                item-value="id"
                hide-no-data
                hide-selected
                clearable
            >
                <template v-slot:item="data">
                    <template v-if="typeof data.item !== 'object'">
                        <v-list-item-content v-text="data.item"></v-list-item-content>
                    </template>
                    <template v-else>
                        <v-list-item-content>
                            <v-list-item-title v-html="data.item.address"></v-list-item-title>
                            <v-list-item-subtitle v-html="data.item.community"></v-list-item-subtitle>
                        </v-list-item-content>
                    </template>
                </template>
            </v-autocomplete>

            <v-text-field
                v-model="title"
                :error-messages="titleErrors"
                label="Назва туру"
                required
                dense
                outlined
                maxlength="255"
                @input="$v.title.$touch()"
                @blur="$v.title.$touch()"
            ></v-text-field>

            <v-textarea
                v-model="description"
                label="Опис"
                dense
                outlined
                rows="3"
            ></v-textarea>

            <v-file-input
                v-model="audioFile"
                placeholder="Додайте файл аудіосупроводу"
                dense
                filled
                prepend-icon="mdi-file-music"
                accept=".mp3"
            ></v-file-input>
        </v-form>

        <v-divider/>

        <div class="damage-form__actions">
            <v-spacer></v-spacer>
            <v-btn
                color="success"
                :loading="formLoading"
                :disabled="formLoading"
                @click="submit"
            >
                Надіслати
                <template v-slot:loader>
                    <span class="custom-loader">
                        <v-icon light>mdi-cached</v-icon>
                    </span>
                </template>
            </v-btn>
        </div>
    </div>
</template>

<script>
import { required } from 'vuelidate/lib/validators';
import { debounce } from '@/js/helpers';

export default {
    name: 'VirtualTourForm',

    props: {

    },

    data() {
        return {
            formLoading: false,
            title: '',
            description: '',
            audioFile: null,
            images: [],

            searchQuery: '',
            damageNoteId: null,
            damageNoteOptions: [],
            isSearching: false,
        }
    },

    validations: {
        title: { required },
    },

    computed: {
        titleErrors() {
            const errors = [];
            if (!this.$v.title.$dirty) return errors;
            !this.$v.title.required && errors.push('Це поле обов\'язкове')
            return errors;
        },
    },

    watch: {
        searchQuery(query) {
            if (query && query.length > 2) {
                this.debouncedSearch(query);
            } else {
                this.damageNoteOptions = []; // Clear options if input is empty
            }
        },
    },

    created() {
        this.debouncedSearch = debounce(this.searchDamageNotes, 500);
    },

    methods: {
        updateImageList(file) {
            this.images = [file.raw];
        },

        handleImageRemove() {
            this.images = [];
        },

        damageNotesFilter(item) {
            const normalizedSearch = this.searchQuery.toLowerCase();
            const address = item.address.toLowerCase();
            const community = item.community.toLowerCase();
            return address.includes(normalizedSearch) || community.includes(normalizedSearch);
        },

        searchDamageNotes(query) {
            this.isSearching = true;
            this.$store.dispatch('searchDamageNotes', { query })
                .then((response) => {
                    this.damageNoteOptions = response.data || [];
                })
                .catch(() => {
                    //
                })
                .finally(() => {
                    this.isSearching = false;
                });
        },

        submit() {
            this.$v.$touch();

            if (this.$v.$invalid) {
                return;
            }

            let data = this.prepareFormData();
            this.$emit('submit-form', data);
        },

        prepareFormData() {
            let data = new FormData();

            data.append('title', this.title);
            data.append('description', this.description);
            data.append('damage_note_id', this.damageNoteId);

            if (this.audioFile) {
                data.append('audio_file', this.audioFile);
            }

            if (this.images && this.images.length) {
                data.append('image', this.images[0]);
            }

            return data;
        },

        clearForm() {
            this.$v.$reset();
            this.title = '';
            this.description = '';
            this.audioFile = null;
            this.images = [];
            this.searchQuery = '';
            this.damageNoteId = null;
            this.damageNoteOptions = [];
            this.isSearching = false;
            this.$refs.fileUpload.clearFiles();
        },
    }
}
</script>

<style lang="scss" scoped>
.damage-form__actions {
    display: flex;
    padding-top: 16px;
}

.custom-loader {
    animation: loader 1s infinite;
    display: flex;
}

@-moz-keyframes loader {
    from {
        transform: rotate(0);
    }
    to {
        transform: rotate(360deg);
    }
}
@-webkit-keyframes loader {
    from {
        transform: rotate(0);
    }
    to {
        transform: rotate(360deg);
    }
}
@-o-keyframes loader {
    from {
        transform: rotate(0);
    }
    to {
        transform: rotate(360deg);
    }
}
@keyframes loader {
    from {
        transform: rotate(0);
    }
    to {
        transform: rotate(360deg);
    }
}
</style>

<style lang="scss">
.damage-form__drag-upload {
    .el-upload-list__item {
        background-color: #F5F7FA;

        .el-icon-close {
            display: inline-block;
        }
    }
}
</style>

