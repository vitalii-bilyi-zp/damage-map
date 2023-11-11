<template>
    <v-container fluid>
        <v-row class="ma-0" justify="center">
            <v-col cols="12" class="py-0">
                <v-snackbar
                    v-model="snackbarSuccess"
                    color="success"
                    top
                >
                    <v-icon dark class="mr-2">mdi-checkbox-marked-circle</v-icon>
                    Інформацію оновлено успішно.
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

                <v-card color="user-card">
                    <v-card-text>
                        <div v-if="isLoading" class="card-progress">
                            <v-progress-circular :size="50" color="primary" indeterminate />
                        </div>

                        <UserForm
                            v-else
                            ref="userForm"
                            edit
                            :user="user"
                            @submit-form="submitForm"
                        />
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
    import UserForm from '@/js/components/UserForm';

    export default {
        name: "UpdatePatient",
        components: {
            UserForm,
        },

        props: ['id'],

        data() {
            return {
                user: null,
                isLoading: false,
                snackbarSuccess: false,
                snackbarError: false,
            }
        },

        mounted() {
            this.loadUser();
        },

        methods: {
            loadUser() {
                this.isLoading = true;
                this.$store.dispatch('loadUser', this.id)
                    .then((response) => {
                        this.user = response.data;
                    })
                    .catch(() => {
                        //
                    })
                    .finally(() => {
                        this.isLoading = false;
                    });
            },

            submitForm(data) {
                const formattedData = {
                    name: data.name,
                    current_password: data.currentPassword,
                    new_password: data.newPassword,
                };

                this.$refs.userForm.formLoading = true;
                this.$store.dispatch('updateUser', { id: this.id, data: formattedData })
                    .then(() => {
                        this.snackbarSuccess = true;
                    })
                    .catch(() => {
                        this.snackbarError = true;
                    })
                    .finally(() => {
                        this.$refs.userForm.formLoading = false;
                    });
            },
        }
    }
</script>

<style lang="scss" scoped>
.user-card {
    width: 100%;
    max-width: 400px;
    margin: 0 auto;
}
</style>
