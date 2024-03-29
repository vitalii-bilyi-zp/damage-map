<template>
    <div class="user-form">
        <v-form>
            <v-text-field
                name="name"
                type="text"
                v-model="form.name"
                dense
                required
                outlined
                maxlength="255"
                :error-messages="nameErrors"
                @blur="$v.form.name.$touch()"
            >
                <template v-slot:label>
                    ПІБ <span v-if="!isEditing" class="red--text">*</span>
                </template>
            </v-text-field>

            <v-text-field
                :disabled="isEditing"
                name="email"
                type="email"
                append-icon="mdi-email"
                v-model="form.email"
                dense
                required
                outlined
                maxlength="255"
                :error-messages="emailErrors"
                @blur="$v.form.email.$touch()"
            >
                <template v-slot:label>
                    E-mail <span v-if="!isEditing" class="red--text">*</span>
                </template>
            </v-text-field>

            <v-select
                v-if="!isEditing || !isPersonalProfile"
                v-model="form.role"
                :items="roleItems"
                :error-messages="roleErrors"
                dense
                required
                outlined
                item-text="display_name"
                item-value="name"
                :disabled="!roleItems || !roleItems.length"
                @change="onRoleChange"
                @blur="$v.form.role.$touch()"
            >
                <template v-slot:label>
                    Роль <span v-if="!isEditing" class="red--text">*</span>
                </template>
            </v-select>

            <template v-if="(!isEditing || !isPersonalProfile) && showRegions">
                <v-autocomplete
                    v-model="form.region"
                    :items="regionItems"
                    label="Регіон"
                    clearable
                    dense
                    outlined
                    item-text="name"
                    item-value="id"
                    :disabled="!regionItems || !regionItems.length"
                    @change="onRegionChange"
                ></v-autocomplete>

                <v-autocomplete
                    v-model="form.district"
                    :items="districtItems"
                    label="Район"
                    clearable
                    dense
                    outlined
                    item-text="name"
                    item-value="id"
                    :disabled="!form.region || !districtItems || !districtItems.length"
                    @change="onDistrictChange"
                ></v-autocomplete>

                <v-autocomplete
                    v-model="form.community"
                    :items="communityItems"
                    label="Територіальна громада"
                    clearable
                    dense
                    outlined
                    item-text="name"
                    item-value="id"
                    :disabled="!form.district || !communityItems || !communityItems.length"
                ></v-autocomplete>
            </template>

            <v-text-field
                v-if="!isEditing"
                name="password"
                type="text"
                v-model="form.password"
                dense
                required
                outlined
                maxlength="255"
                append-icon='mdi-refresh'
                @click:append="generatePassword"
                :error-messages="passwordErrors"
                @blur="$v.form.password.$touch()"
            >
                <template v-slot:label>
                    Пароль <span class="red--text">*</span>
                </template>
            </v-text-field>

            <template v-else-if="isPersonalProfile">
                <v-text-field
                    label="Поточний пароль"
                    name="current_password"
                    v-model="form.currentPassword"
                    dense
                    outlined
                    maxlength="255"
                    :append-icon="showCurrentPassword ? 'mdi-eye' : 'mdi-eye-off'"
                    :type="showCurrentPassword ? 'text' : 'password'"
                    :error-messages="currentPasswordErrors"
                    @click:append="showCurrentPassword = !showCurrentPassword"
                    @blur="$v.form.currentPassword.$touch()"
                ></v-text-field>

                <v-text-field
                    label="Новий пароль"
                    name="new_password"
                    v-model="form.newPassword"
                    dense
                    outlined
                    maxlength="255"
                    :append-icon="showNewPassword ? 'mdi-eye' : 'mdi-eye-off'"
                    :type="showNewPassword ? 'text' : 'password'"
                    :error-messages="newPasswordErrors"
                    @click:append="showNewPassword = !showNewPassword"
                    @blur="$v.form.newPassword.$touch()"
                ></v-text-field>
            </template>
        </v-form>

        <v-divider/>

        <div class="user-form__actions">
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
    import { required, requiredIf, email, minLength } from 'vuelidate/lib/validators';

    export default {
        name: "UserForm",

        props: {
            user: {
                type: Object,
                default: null
            },
            isPersonalProfile: {
                type: Boolean,
                default: false
            },
            roleItems: {
                type: Array,
                default: () => []
            },
            regionItems: {
                type: Array,
                default: () => []
            },
        },

        validations() {
            let rules = {
                form: {
                    name: {},
                    email: {},
                    role: {},
                    password: {},
                    currentPassword: {},
                    newPassword: {},
                }
            };

            if (!this.isEditing) {
                rules.form.name = {
                    required,
                };
                rules.form.email = {
                    required,
                    email,
                };
                rules.form.role = {
                    required,
                };
                rules.form.password = {
                    required,
                    minLength: minLength(8),
                };
            } else if (this.isPersonalProfile) {
                rules.form.currentPassword = {
                    required: requiredIf('newPassword'),
                    minLength: minLength(8),
                };
                rules.form.newPassword = {
                    required: requiredIf('currentPassword'),
                    minLength: minLength(8),
                };
            }

            return rules;
        },

        data() {
            return {
                formLoading: false,
                passwordSize: 10,
                passwordCharacters: 'a-z,A-Z,0-9,#',
                showCurrentPassword: false,
                showNewPassword: false,
                form: {
                    name: null,
                    email: null,
                    role: null,
                    region: null,
                    district: null,
                    community: null,
                    password: null,
                    currentPassword: null,
                    newPassword: null
                },
                districtItems: [],
                communityItems: [],
            }
        },

        computed: {
            isEditing() {
                return !!this.user;
            },

            nameErrors() {
                if (!this.isEditing) {
                    const errors = [];
                    if (!this.$v.form.name.$dirty) return errors;
                    !this.$v.form.name.required && errors.push('Це поле обов\'язкове');
                    return errors;
                }

                return [];
            },

            emailErrors() {
                if (!this.isEditing) {
                    const errors = [];
                    if (!this.$v.form.email.$dirty) return errors;
                    !this.$v.form.email.required && errors.push('Це поле обов\'язкове');
                    !this.$v.form.email.email && errors.push('Неправильний формат електронної адреси.');
                    return errors;
                }

                return [];
            },

            roleErrors() {
                if (!this.isEditing) {
                    const errors = [];
                    if (!this.$v.form.role.$dirty) return errors;
                    !this.$v.form.role.required && errors.push('Це поле обов\'язкове');
                    return errors;
                }

                return [];
            },

            passwordErrors() {
                if (!this.isEditing) {
                    const errors = [];
                    if (!this.$v.form.password.$dirty) return errors;
                    !this.$v.form.password.required && errors.push('Це поле обов\'язкове');
                    !this.$v.form.password.minLength && errors.push('Мінімальна довжина пароля 8 символів.');
                    return errors;
                }

                return [];
            },

            currentPasswordErrors() {
                if (this.isEditing && this.isPersonalProfile) {
                    const errors = [];
                    if (!this.$v.form.currentPassword.$dirty) return errors;
                    !this.$v.form.currentPassword.required && errors.push('Це поле обов\'язкове');
                    !this.$v.form.currentPassword.minLength && errors.push('Мінімальна довжина пароля 8 символів.');
                    return errors;
                }

                return [];
            },

            newPasswordErrors() {
                if (this.isEditing && this.isPersonalProfile) {
                    const errors = [];
                    if (!this.$v.form.newPassword.$dirty) return errors;
                    !this.$v.form.newPassword.required && errors.push('Це поле обов\'язкове');
                    !this.$v.form.newPassword.minLength && errors.push('Мінімальна довжина пароля 8 символів.');
                    return errors;
                }

                return [];
            },

            showRegions() {
                return this.form.role === 'admin';
            },
        },

        watch: {
            user() {
                this.initForm();
            },

            regionItems() {
                this.setDistrictItems();
                this.setCommunityItems();
            }
        },

        mounted() {
            this.initForm();

            if (!this.isEditing) {
                this.generatePassword();
            }
        },

        methods: {
            onRoleChange() {
                this.form.region = null;
                this.form.district = null;
                this.form.community = null;
                this.$v.form.role.$touch();
            },

            onRegionChange() {
                this.form.district = null;
                this.form.community = null;

                this.setDistrictItems();
            },

            setDistrictItems() {
                let items = [];

                if (this.form.region) {
                    const region = this.regionItems.find((item) => item.id === this.form.region);
                    items = region.districts;
                }

                this.districtItems = items;
            },

            onDistrictChange() {
                this.form.community = null;

                this.setCommunityItems();
            },

            setCommunityItems() {
                let items = [];

                if (this.form.district) {
                    const district = this.districtItems.find((item) => item.id === this.form.district);
                    items = district.communities;
                }

                this.communityItems = items;
            },

            generatePassword() {
                let charactersArray = this.passwordCharacters.split(',');
                let CharacterSet = '';
                let password = '';

                if( charactersArray.indexOf('a-z') >= 0) {
                    CharacterSet += 'abcdefghijklmnopqrstuvwxyz';
                }
                if( charactersArray.indexOf('A-Z') >= 0) {
                    CharacterSet += 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                }
                if( charactersArray.indexOf('0-9') >= 0) {
                    CharacterSet += '0123456789';
                }
                if( charactersArray.indexOf('#') >= 0) {
                    CharacterSet += '![]{}()%&*$#^<>~@|';
                }

                for(let i = 0; i < this.passwordSize; i++) {
                    password += CharacterSet.charAt(Math.floor(Math.random() * CharacterSet.length));
                }

                this.$set(this.form, 'password', password);
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
                let data = {
                    name: this.form.name || null,
                    role: this.form.role || null,
                    region: this.form.region || null,
                    district: this.form.district || null,
                    community: this.form.community || null,
                };

                if (!this.isEditing) {
                    data.email = this.form.email || null;
                    data.password = this.form.password || null;
                } else if (this.isPersonalProfile) {
                    data.currentPassword = this.form.currentPassword || null;
                    data.newPassword = this.form.newPassword || null;
                }

                return data;
            },

            initForm() {
                this.form = {
                    name: this.user ? this.user.name : null,
                    email: this.user ? this.user.email : null,
                    role: this.user ? this.user.role : null,
                    region: this.user ? this.user.region_id : null,
                    district: this.user ? this.user.district_id : null,
                    community: this.user ? this.user.community_id : null,
                    password: null,
                    currentPassword: null,
                    newPassword: null,
                };

                this.setDistrictItems();
                this.setCommunityItems();
            },

            clearForm() {
                this.$v.$reset();
                this.showCurrentPassword = false;
                this.showNewPassword = false;
                this.form = {
                    name: null,
                    email: null,
                    role: null,
                    region: null,
                    district: null,
                    community: null,
                    password: null,
                    currentPassword: null,
                    newPassword: null,
                };
                this.districtItems = [];
                this.communityItems = [];

                if (!this.isEditing) {
                    this.generatePassword();
                }
            },
        }
    }
</script>

<style lang="scss" scoped>
.user-form__actions {
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
