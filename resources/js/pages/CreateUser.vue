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

                <v-card color="user-card">
                    <v-card-text>
                        <UserForm
                            ref="userForm"
                            :role-items="roleItems"
                            :region-items="regionItems"
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
        name: "CreateUser",
        components: {
            UserForm,
        },

        data() {
            return {
                roleItems: [],
                regionItems: [],
                rolesLoading: false,
                regionsLoading: false,
                snackbarSuccess: false,
                snackbarError: false,
            }
        },

        mounted() {
            this.loadRoles();
            this.loadRegions();
        },

        methods: {
            loadRoles() {
                this.rolesLoading = true;
                this.$store.dispatch('loadRoles')
                    .then((response) => {
                        this.roleItems = response.data || [];
                    })
                    .catch(() => {
                        //
                    })
                    .finally(() => {
                        this.rolesLoading = false;
                    });
            },

            loadRegions() {
                this.regionsLoading = true;
                this.$store.dispatch('loadRegions', { loadDetails: true })
                    .then((response) => {
                        this.regionItems = response.data || [];
                    })
                    .catch(() => {
                        //
                    })
                    .finally(() => {
                        this.regionsLoading = false;
                    });
            },

            submitForm(data) {
                const formattedData = {
                    name: data.name,
                    email: data.email,
                    role: data.role,
                    region: data.region,
                    district: data.district,
                    community: data.community,
                    password: data.password,
                };

                this.$refs.userForm.formLoading = true;
                this.$store.dispatch('saveUser', { data: formattedData })
                    .then(() => {
                        this.$refs.userForm.clearForm();
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
