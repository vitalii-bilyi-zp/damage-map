<template>
    <div class="damage-form">
        <v-form v-if="fileUploading">
            <v-file-input
                v-model="file"
                :error-messages="fileErrors"
                placeholder="Оберіть файл"
                dense
                filled
                prepend-icon="mdi-file-table"
                accept=".csv, application/vnd.ms-excel, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
                @change="$v.file.$touch()"
                @blur="$v.file.$touch()"
            ></v-file-input>
        </v-form>

        <v-form v-else>
            <template v-if="isEditing || !isAuthorized">
                <v-text-field
                    v-model="fullName"
                    :error-messages="fullNameErrors"
                    :disabled="isEditing"
                    label="ПІБ"
                    dense
                    required
                    outlined
                    maxlength="255"
                    @input="$v.fullName.$touch()"
                    @blur="$v.fullName.$touch()"
                ></v-text-field>

                <v-text-field
                    v-model="email"
                    :error-messages="emailErrors"
                    :disabled="isEditing"
                    label="E-mail"
                    type="email"
                    append-icon="mdi-email"
                    dense
                    required
                    outlined
                    maxlength="255"
                    @input="$v.email.$touch()"
                    @blur="$v.email.$touch()"
                ></v-text-field>

                <v-text-field
                    v-model="phone"
                    :error-messages="phoneErrors"
                    :disabled="isEditing"
                    v-mask="'+38 (0##) ### - ## - ##'"
                    label="Телефон"
                    type="tel"
                    append-icon="mdi-phone"
                    dense
                    required
                    outlined
                    maxlength="255"
                    @input="$v.phone.$touch()"
                    @blur="$v.phone.$touch()"
                ></v-text-field>
            </template>

            <v-menu
                v-model="dateMenu"
                :close-on-content-click="false"
                transition="scale-transition"
                offset-y
                min-width="280px"
            >
                <template v-slot:activator="{ on }">
                    <v-text-field
                        v-on="on"
                        v-model="date"
                        :error-messages="dateErrors"
                        label="Дата"
                        dense
                        outlined
                        readonly
                        append-icon="mdi-calendar"
                        @blur="$v.date.$touch()"
                    ></v-text-field>
                </template>
                <v-date-picker
                    v-model="date"
                    min="2022-02-24"
                    :max="new Date().toISOString().substr(0, 10)"
                    locale="uk-UA"
                    @input="dateMenu = false"
                ></v-date-picker>
            </v-menu>

            <v-select
                v-model="objectCategory"
                :items="objectCategoryItemsComputed"
                :error-messages="objectCategoryErrors"
                label="Категорія об’єкта"
                dense
                required
                outlined
                item-text="name"
                item-value="id"
                :disabled="!objectCategoryItemsComputed || !objectCategoryItemsComputed.length"
                @change="onObjectCategoryChange"
                @blur="$v.objectCategory.$touch()"
            ></v-select>

            <v-select
                v-model="objectType"
                :items="objectTypeItemsComputed"
                :error-messages="objectTypeErrors"
                label="Тип об’єкта"
                dense
                required
                outlined
                item-text="name"
                item-value="id"
                :disabled="!objectCategory || !objectTypeItemsComputed || !objectTypeItemsComputed.length"
                @change="$v.objectType.$touch()"
                @blur="$v.objectType.$touch()"
            ></v-select>

            <v-autocomplete
                v-model="community"
                :items="communityItems"
                :error-messages="communityErrors"
                label="Територіальна громада"
                dense
                required
                outlined
                item-text="name"
                item-value="id"
                :disabled="!communityItems || !communityItems.length"
                @change="$v.community.$touch()"
                @blur="$v.community.$touch()"
            ></v-autocomplete>

            <v-text-field
                v-model="city"
                label="Місто / селище"
                dense
                outlined
            ></v-text-field>

            <v-text-field
                v-model="street"
                label="Вулиця"
                dense
                outlined
            ></v-text-field>

            <v-text-field
                v-model="buildingNumber"
                label="Будівля"
                dense
                outlined
            ></v-text-field>

            <v-select
                v-model="damageType"
                :items="damageTypeItems"
                :error-messages="damageTypeErrors"
                label="Тип пошкодження"
                dense
                required
                outlined
                item-text="name"
                item-value="id"
                @change="$v.damageType.$touch()"
                @blur="$v.damageType.$touch()"
            ></v-select>

            <v-text-field
                v-model="restorationСost"
                :error-messages="restorationСostErrors"
                label="Вартість відновлення"
                type="number"
                dense
                prefix="₴"
                required
                outlined
                @input="$v.restorationСost.$touch()"
                @blur="$v.restorationСost.$touch()"
            ></v-text-field>

            <v-textarea
                v-model="comment"
                label="Коментар"
                dense
                outlined
                rows="3"
            ></v-textarea>

            <v-divider/>

            <div v-if="!isEditing" class="damage-form__file-upload">
                <el-upload
                    ref="fileUpload"
                    action="https://jsonplaceholder.typicode.com/posts/"
                    list-type="picture-card"
                    :on-change="updateImageList"
                    :on-preview="handleImagePreview"
                    :on-remove="handleImageRemove"
                    :auto-upload="false"
                >
                    <i class="el-icon-plus" />
                </el-upload>
                <el-dialog :visible.sync="dialogVisible">
                    <img width="100%" :src="dialogImageUrl" alt>
                </el-dialog>
            </div>
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
import moment from 'moment';
import { required, email } from 'vuelidate/lib/validators';
import {mask} from 'vue-the-mask';

export default {
    name: 'DamageForm',

    directives: { mask },

    props: {
        isAuthorized: {
            type: Boolean,
            default: false
        },
        fileUploading: {
            type: Boolean,
            default: false
        },
        objectTypeItems: {
            type: Array,
            default: () => []
        },
        communityItems: {
            type: Array,
            default: () => []
        },
        damageNote: {
            type: Object,
            default: null
        },
    },

    data() {
        return {
            formLoading: false,
            fullName: null,
            email: null,
            phone: null,
            dateMenu: false,
            date: moment().format('YYYY-MM-DD'),
            objectCategory: null,
            objectType: null,
            community: null,
            city: null,
            street: null,
            buildingNumber: null,
            damageType: null,
            damageTypeItems: [
                {
                    id: 'high',
                    name: 'Повне руйнування',
                },
                {
                    id: 'medium',
                    name: 'Сильне руйнування',
                },
                {
                    id: 'low',
                    name: 'Слабке руйнування',
                }
            ],
            restorationСost: null,
            comment: null,
            file: null,
            images: [],
            dialogImageUrl: '',
            dialogVisible: false,
        }
    },

    validations() {
        let rules = {
            date: { required },
            objectCategory: { required },
            objectType: { required },
            community: { required },
            damageType: { required },
            restorationСost: { required },
            file: { required },

            formValidationGroup: ['date', 'objectCategory', 'objectType', 'community', 'damageType', 'restorationСost']
        };

        if (!this.isAuthorized) {
            rules = Object.assign(rules, {
                fullName: { required },
                email: { required, email },
                phone: { required },

                formValidationGroup: ['fullName', 'email', 'phone', 'date', 'objectCategory', 'objectType', 'community', 'damageType', 'restorationСost']
            });
        }

        return rules;
    },

    computed: {
        isEditing() {
            return !!this.damageNote;
        },

        fullNameErrors() {
            const errors = [];
            if (!this.$v.fullName.$dirty) return errors;
            !this.$v.fullName.required && errors.push('Це поле обов\'язкове');
            return errors;
        },

        emailErrors() {
            const errors = [];
            if (!this.$v.email.$dirty) return errors;
            !this.$v.email.required && errors.push('Це поле обов\'язкове');
            !this.$v.email.email && errors.push('Неправильний формат електронної адреси.');
            return errors;
        },

        phoneErrors() {
            const errors = [];
            if (!this.$v.phone.$dirty) return errors;
            !this.$v.phone.required && errors.push('Це поле обов\'язкове');
            return errors;
        },

        dateErrors() {
            const errors = [];
            if (!this.$v.date.$dirty) return errors;
            !this.$v.date.required && errors.push('Це поле обов\'язкове')
            return errors;
        },

        objectCategoryErrors() {
            const errors = [];
            if (!this.$v.objectCategory.$dirty) return errors;
            !this.$v.objectCategory.required && errors.push('Це поле обов\'язкове')
            return errors;
        },

        objectTypeErrors() {
            const errors = [];
            if (!this.$v.objectType.$dirty) return errors;
            !this.$v.objectType.required && errors.push('Це поле обов\'язкове')
            return errors;
        },

        communityErrors() {
            const errors = [];
            if (!this.$v.community.$dirty) return errors;
            !this.$v.community.required && errors.push('Це поле обов\'язкове')
            return errors;
        },

        damageTypeErrors() {
            const errors = [];
            if (!this.$v.damageType.$dirty) return errors;
            !this.$v.damageType.required && errors.push('Це поле обов\'язкове')
            return errors;
        },

        restorationСostErrors() {
            const errors = [];
            if (!this.$v.restorationСost.$dirty) return errors;
            !this.$v.restorationСost.required && errors.push('Це поле обов\'язкове')
            return errors;
        },

        fileErrors() {
            const errors = [];
            if (!this.$v.file.$dirty) return errors;
            !this.$v.file.required && errors.push('Це поле обов\'язкове')
            return errors;
        },

        objectCategoryItemsComputed() {
            const objectCategories = this.objectTypeItems.reduce((prev, curr) => {
                if (curr.object_category && !prev[curr.object_category.id]) {
                    prev[curr.object_category.id] = curr.object_category;
                }

                return prev;
            }, {});

            return Object.values(objectCategories);
        },

        objectTypeItemsComputed() {
            if (!this.objectCategory) {
                return [];
            }

            return this.objectTypeItems.filter((item) => item.object_category_id === this.objectCategory);
        }
    },

    watch: {
        damageNote() {
            this.initForm();
        },
    },

    mounted() {
        this.initForm();
    },

    methods: {
        onObjectCategoryChange() {
            this.objectType = null;
            this.$v.objectType.$reset();
            this.$v.objectCategory.$touch();
        },

        submit() {
            this.fileUploading ? this.submitFile() : this.submitForm();
        },

        submitFile() {
            this.$v.file.$touch();

            if (this.$v.file.$invalid) {
                return;
            }

            let data = this.prepareFileData();
            this.$emit('submit-file', data);
        },

        prepareFileData() {
            let data = new FormData();
            data.append('file', this.file);

            return data;
        },

        clearFile() {
            this.$v.file.$reset();
            this.file = null;
        },

        submitForm() {
            this.$v.formValidationGroup.$touch();

            if (this.$v.formValidationGroup.$invalid) {
                return;
            }

            let data = this.prepareFormData();
            this.$emit('submit-form', data);
        },

        prepareFormData() {
            return {
                fullName: this.fullName,
                email: this.email,
                phone: this.phone,
                date: this.date,
                objectCategory: this.objectCategory,
                objectType: this.objectType,
                community: this.community,
                city: this.city,
                street: this.street,
                buildingNumber: this.buildingNumber,
                damageType: this.damageType,
                restorationСost: this.restorationСost,
                comment: this.comment,
                images: this.images,
            };
        },

        initForm() {
            this.fullName = this.damageNote ? this.damageNote.fullName : null;
            this.email = this.damageNote ? this.damageNote.email : null;
            this.phone = this.damageNote ? this.damageNote.phone : null;
            this.date = this.damageNote ? this.damageNote.date : moment().format('YYYY-MM-DD');
            this.objectCategory = this.damageNote ? this.damageNote.objectCategory : null;
            this.objectType = this.damageNote ? this.damageNote.objectType : null;
            this.community = this.damageNote ? this.damageNote.community : null;
            this.city = this.damageNote ? this.damageNote.city : null;
            this.street = this.damageNote ? this.damageNote.street : null;
            this.buildingNumber = this.damageNote ? this.damageNote.buildingNumber : null;
            this.damageType = this.damageNote ? this.damageNote.damageType : null;
            this.restorationСost = this.damageNote ? this.damageNote.restorationСost : null;
            this.comment = this.damageNote ? this.damageNote.comment : null;
        },

        clearForm() {
            this.$v.formValidationGroup.$reset();
            this.fullName = null;
            this.email = null;
            this.phone = null;
            this.date = moment().format('YYYY-MM-DD');
            this.objectCategory = null;
            this.objectType = null;
            this.community = null;
            this.city = null;
            this.street = null;
            this.buildingNumber = null;
            this.damageType = null;
            this.restorationСost = null;
            this.comment = null;
            this.images = [];
            this.dialogImageUrl = '';
            this.dialogVisible = false;

            this.$refs.fileUpload.clearFiles();
        },

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
.damage-form__file-upload {
    padding-top: 15px;

    .el-upload-list--picture-card {
        padding-left: 0;
    }

    .el-upload--picture-card {
        margin-bottom: 15px;
    }
}
</style>

