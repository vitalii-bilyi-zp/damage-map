<template>
    <div class="damage-notes">
        <v-snackbar
            v-model="snackbarSuccess"
            color="success"
            top
        >
            <v-icon dark class="mr-2">mdi-checkbox-marked-circle</v-icon>
            Інформація прийнята в обробку.
            <v-btn
                text
                icon
                dark
                @click="snackbarSuccess = false">
                <v-icon size="20">mdi-close</v-icon>
            </v-btn>
        </v-snackbar>

        <v-snackbar
            v-model="snackbarError"
            color="error"
            top
        >
            <v-icon dark class="mr-2">mdi-alert-circle</v-icon>
            Сталася помилка. Будь ласка, спробуйте пізніше.
            <v-btn
                text
                icon
                dark
                @click="snackbarError = false">
                <v-icon size="20">mdi-close</v-icon>
            </v-btn>
        </v-snackbar>

        <v-card color="damage-notes__card">
            <v-card-title :class="[fileUploading ? '' : 'd-flex justify-center']">
                <v-btn v-if="fileUploading" key="back" icon @click="fileUploading = false">
                    <v-icon>mdi-arrow-left</v-icon>
                </v-btn>

                <v-btn v-else key="upload" outlined color="primary" @click="fileUploading = true">
                    Завантажити файл
                    <v-icon right dark>
                        mdi-upload
                    </v-icon>
                </v-btn>
            </v-card-title>

            <v-divider/>

            <v-card-text class="damage-notes__card-text">
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
                        :disabled="objectTypesLoading"
                        @change="$v.objectCategory.$touch()"
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
                        :disabled="objectTypesLoading || !objectCategory"
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
                        :disabled="communitiesLoading"
                        @change="$v.community.$touch()"
                        @blur="$v.community.$touch()"
                    ></v-autocomplete>

                    <v-text-field
                        v-model="city"
                        :error-messages="cityErrors"
                        label="Місто / селище"
                        dense
                        required
                        outlined
                        @input="$v.city.$touch()"
                        @blur="$v.city.$touch()"
                    ></v-text-field>

                    <v-text-field
                        v-model="street"
                        :error-messages="streetErrors"
                        label="Вулиця"
                        dense
                        required
                        outlined
                        @input="$v.street.$touch()"
                        @blur="$v.street.$touch()"
                    ></v-text-field>

                    <v-text-field
                        v-model="buildingNumber"
                        :error-messages="buildingNumberErrors"
                        label="Будівля"
                        dense
                        required
                        outlined
                        @input="$v.buildingNumber.$touch()"
                        @blur="$v.buildingNumber.$touch()"
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
                </v-form>
            </v-card-text>

            <v-divider/>

            <v-card-actions class="damage-notes__card-actions">
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
            </v-card-actions>
        </v-card>
    </div>
</template>

<script>
    import { required } from 'vuelidate/lib/validators'

    export default {
        name: 'DamageNotes',

        data() {
            return {
                objectTypesLoading: false,
                communitiesLoading: false,
                formLoading: false,
                objectCategory: null,
                objectType: null,
                objectTypeItems: [],
                community: null,
                communityItems: [],
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
                snackbarSuccess: false,
                snackbarError: false,
                fileUploading: false,
                file: null,
            }
        },

        validations: {
            objectCategory: { required },
            objectType: { required },
            community: { required },
            city: { required },
            street: { required },
            buildingNumber: { required },
            damageType: { required },
            restorationСost: { required },
            file: { required },

            formValidationGroup: ['objectCategory', 'objectType', 'community', 'city', 'street', 'buildingNumber', 'damageType', 'restorationСost']
        },

        computed: {
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

            cityErrors() {
                const errors = [];
                if (!this.$v.city.$dirty) return errors;
                !this.$v.city.required && errors.push('Це поле обов\'язкове')
                return errors;
            },

            streetErrors() {
                const errors = [];
                if (!this.$v.street.$dirty) return errors;
                !this.$v.street.required && errors.push('Це поле обов\'язкове')
                return errors;
            },

            buildingNumberErrors() {
                const errors = [];
                if (!this.$v.buildingNumber.$dirty) return errors;
                !this.$v.buildingNumber.required && errors.push('Це поле обов\'язкове')
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
                }, {})

                return Object.values(objectCategories);
            },

            objectTypeItemsComputed() {
                if (!this.objectCategory) {
                    return [];
                }

                return this.objectTypeItems.filter((item) => item.object_category_id === this.objectCategory);
            }
        },

        mounted() {
            this.loadObjectTypes();
            this.loadCommunities();
        },

        methods: {
            loadObjectTypes() {
                this.objectTypesLoading = true;
                this.$store.dispatch('loadObjectTypes')
                    .then((response) => {
                        this.objectTypeItems = response.data || [];
                    })
                    .catch(() => {
                        //
                    })
                    .finally(() => {
                        this.objectTypesLoading = false;
                    });
            },

            loadCommunities() {
                this.communitiesLoading = true;
                this.$store.dispatch('loadCommunities')
                    .then((response) => {
                        this.communityItems = response.data || [];
                    })
                    .catch(() => {
                        //
                    })
                    .finally(() => {
                        this.communitiesLoading = false;
                    });
            },

            submit() {
                this.fileUploading ? this.submitFile() : this.submitForm();
            },

            submitFile() {
                this.$v.file.$touch();

                if (this.$v.file.$invalid) {
                    return;
                }

                this.formLoading = true;
                let data = this.prepareFileData();
                this.$store.dispatch('saveDamageNotesFromFile', data)
                    .then(() => {
                        this.clearFile();
                        this.snackbarSuccess = true;
                    })
                    .catch(() => {
                        this.snackbarError = true;
                    })
                    .finally(() => {
                        this.formLoading = false;
                    });
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

                this.formLoading = true;
                let data = this.prepareFormData();
                this.$store.dispatch('saveDamageNotes', data)
                    .then(() => {
                        this.clearForm();
                        this.snackbarSuccess = true;
                    })
                    .catch(() => {
                        this.snackbarError = true;
                    })
                    .finally(() => {
                        this.formLoading = false;
                    });
            },

            prepareFormData() {
                return {
                    object_type_id: this.objectType,
                    community_id: this.community,
                    city: this.city,
                    street: this.street,
                    building_number: this.buildingNumber,
                    damage_type: this.damageType,
                    restoration_cost: this.restorationСost,
                };
            },

            clearForm() {
                this.$v.formValidationGroup.$reset();
                this.objectCategory = null;
                this.objectType = null;
                this.community = null;
                this.city = null;
                this.street = null;
                this.buildingNumber = null;
                this.damageType = null;
                this.restorationСost = null;
            },
        }
    }
</script>

<style lang="scss" scoped>
.damage-notes {
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 16px;
}

.damage-notes__card {
    width: 100%;
    max-width: 400px;
}

.damage-notes__card-text {
    padding-bottom: 0;
}

.damage-notes__card-actions {
    padding: 16px;
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

