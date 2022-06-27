<template>
    <form>
        <v-select
            v-model="objectCategory"
            :items="objectCategoryItemsComputed"
            :error-messages="objectCategoryErrors"
            label="Категорія об’єкта"
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
            required
            outlined
            @input="$v.city.$touch()"
            @blur="$v.city.$touch()"
        ></v-text-field>

        <v-text-field
            v-model="streetName"
            :error-messages="streetNameErrors"
            label="Вулиця"
            required
            outlined
            @input="$v.streetName.$touch()"
            @blur="$v.streetName.$touch()"
        ></v-text-field>

        <v-text-field
            v-model="buildingNumber"
            :error-messages="buildingNumberErrors"
            label="Будівля"
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
            required
            outlined
            @change="$v.damageType.$touch()"
            @blur="$v.damageType.$touch()"
        ></v-select>

        <v-text-field
            v-model="restorationСost"
            :error-messages="restorationСostErrors"
            label="Вартість відновлення"
            required
            outlined
            @input="$v.restorationСost.$touch()"
            @blur="$v.restorationСost.$touch()"
        ></v-text-field>

        <v-btn class="mr-4" @click="clear">
            Clear
        </v-btn>

        <v-btn @click="submit">
            Submit
        </v-btn>
    </form>
</template>

<script>
    import { required } from 'vuelidate/lib/validators'

    export default {
        name: 'DamageForm',

        data() {
            return {
                objectTypesLoading: false,
                communitiesLoading: false,

                objectCategory: null,
                objectType: null,
                objectTypeItems: [],
                community: null,
                communityItems: [],
                city: null,
                streetName: null,
                buildingNumber: null,
                damageType: null,
                damageTypeItems: [
                    'Повне руйнування',
                    'Сильне руйнування',
                    'Слабке руйнування',
                ],
                restorationСost: null,
            }
        },

        validations: {
            objectCategory: { required },
            objectType: { required },
            community: { required },
            city: { required },
            streetName: { required },
            buildingNumber: { required },
            damageType: { required },
            restorationСost: { required },
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

            streetNameErrors() {
                const errors = [];
                if (!this.$v.streetName.$dirty) return errors;
                !this.$v.streetName.required && errors.push('Це поле обов\'язкове')
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

            clear() {
                this.$v.$reset();
                this.objectCategory = null;
                this.objectType = null;
                this.community = null;
                this.city = null;
                this.streetName = null;
                this.buildingNumber = null;
                this.damageType = null;
                this.restorationСost = null;
            },

            submit() {
                this.$v.$touch();
            },
        }
    }
</script>

<style>

</style>

