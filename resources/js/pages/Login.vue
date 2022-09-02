<template>
    <div class="login">
        <v-snackbar
            v-model="snackbar"
            top
            multi-line
            color="error"
        >
            <v-list-item two-line dark>
                <v-list-item-content>
                    <v-list-item-title>Помилка авторизації!</v-list-item-title>
                    <v-list-item-subtitle>Введено невірний логін або пароль.</v-list-item-subtitle>
                </v-list-item-content>
            </v-list-item>

            <template v-slot:action="{ attrs }">
                <v-btn text icon dark v-bind="attrs" @click="snackbar = false">
                    <v-icon size="20">mdi-close</v-icon>
                </v-btn>
            </template>
        </v-snackbar>

        <v-card class="form-card elevation-2">
            <v-card-text class="card-content">
                <v-form>
                    <v-text-field
                        label="E-mail"
                        name="email"
                        prepend-icon="mdi-email"
                        type="email"
                        v-model="email"
                        maxlength="255"
                        :error-messages="emailErrors"
                        @blur="$v.email.$touch()"
                        @keyup.native.enter="submit"
                    ></v-text-field>

                    <v-text-field
                        label="Пароль"
                        name="password"
                        prepend-icon="mdi-lock"
                        :append-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
                        @click:append="showPassword = !showPassword"
                        :type="showPassword ? 'text' : 'password'"
                        v-model="password"
                        maxlength="20"
                        :error-messages="passwordErrors"
                        @blur="$v.password.$touch()"
                        @keyup.native.enter="submit"
                    ></v-text-field>

                    <div class="remember">
                        <v-checkbox
                            label="Запам'ятати мене"
                            color="primary"
                            hide-details
                            v-model="remember"
                            class="mt-2"
                        ></v-checkbox>
                    </div>
                </v-form>
            </v-card-text>
            <v-card-actions class="card-actions">
                <v-btn
                    color="primary"
                    class="form-submit"
                    :loading="isLoading"
                    @click.prevent="submit"
                >
                    Увійти
                </v-btn>
                <div>
                    <router-link
                        :to="{ name: 'map' }"
                        class="back-link"
                    >
                        <!-- <span class="lnr lnr-arrow-left back-link__icon"></span> -->
                        <v-icon color="primary" class="back-link__icon">
                            mdi-arrow-left-thin
                        </v-icon>
                        На головну сторінку
                    </router-link>
                </div>
            </v-card-actions>
        </v-card>
    </div>
</template>

<script>
    import { required, email, minLength } from 'vuelidate/lib/validators';

    export default {
        name: "Login",

        data() {
            return {
                email: '',
                password: '',
                remember: false,
                showPassword: false,
                isLoading: false,
                snackbar: false
            }
        },

        validations: {
            email: {
                required,
                email
            },
            password: {
                required,
                minLength: minLength(8)
            }
        },

        computed: {
            emailErrors() {
                const errors = [];
                if (!this.$v.email.$dirty) return errors;
                !this.$v.email.required && errors.push('Це поле обов\'язкове');
                !this.$v.email.email && errors.push('Некоректний формат електронної адреси.');
                return errors;
            },
            passwordErrors() {
                const errors = [];
                if (!this.$v.password.$dirty) return errors;
                !this.$v.password.required && errors.push('Це поле обов\'язкове');
                !this.$v.password.minLength && errors.push('Мінімальна довжина пароля 8 символів.');
                return errors;
            },
        },

        methods: {
            submit() {
                this.$v.$touch();

                if (this.$v.$invalid) {
                    return;
                }

                const {email, password, remember} = this;

                this.isLoading = true;
                this.snackbar = false;
                this.$store.dispatch('login', {data: {email, password}, remember})
                    .then(() => {
                        if (this.$route.params.nextUrl != null) {
                            this.$router.push(this.$route.params.nextUrl);
                        } else {
                            this.$router.push({ name: 'damage-notes' });
                        }
                    })
                    .catch((error) => {
                        if (error.response && error.response.status === 401) {
                            this.snackbar = true;
                        }
                    })
                    .finally(() => {
                        this.isLoading = false;
                    });
            },
        },
    }
</script>

<style lang="scss" scoped>
    .login {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        min-height: 100vh;
        padding: 12px;
    }

    .form-card {
        width: 100%;
        max-width: 400px;
        padding: 30px 40px;
    }

    .card-content {
        padding: 0;
    }

    .card-actions {
        padding: 0;
        flex-wrap: wrap;
        justify-content: center;
    }

    .form-submit {
        width: 100%;
        margin: 20px 0 25px;
    }

    .remember {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        font-size: 1rem;
    }

    .back-link {
        position: relative;
        display: inline-block;
        padding-left: 30px;
        line-height: 24px;

        &:hover .back-link__icon {
            left: 0;
        }
    }

    .back-link__icon {
        position: absolute;
        top: 0;
        left: 4px;
        transition: all 0.3s ease 0s;
    }
</style>
