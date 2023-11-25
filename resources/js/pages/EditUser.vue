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
                            :user="user"
                            :is-personal-profile="isPersonalProfile"
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
    import store from '@/js/store/index';

    export default {
        name: "EditUser",
        components: {
            UserForm,
        },

        props: ['id'],

        data() {
            return {
                user: null,
                isLoading: false,
                roleItems: [],
                regionItems: [],
                rolesLoading: false,
                regionsLoading: false,
                snackbarSuccess: false,
                snackbarError: false,
            }
        },

        computed: {
            isSuperAdmin() {
                return this.$store.getters.isSuperAdmin;
            },

            isPersonalProfile() {
                return this.$store.state.currentUser && this.$store.state.currentUser.id === this.id;
            }
        },

        beforeRouteEnter(to, from, next) {
            if (!store.getters.isSuperAdmin && store.state.currentUser.id !== to.params.id) {
                return next(from.fullPath);
            }

            return next();
        },

        mounted() {
            if (!this.isPersonalProfile) {
                this.loadRoles();
                this.loadRegions();
            }

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
                    role: data.role,
                    region: data.region,
                    district: data.district,
                    community: data.community,
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
