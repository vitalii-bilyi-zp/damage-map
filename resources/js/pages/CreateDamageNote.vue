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

                <v-card color="damage-note-card">
                    <template v-if="isAuthorized">
                        <v-card-title :class="[fileUploading ? '' : 'd-flex justify-center']">
                            <v-btn v-if="fileUploading" key="back" icon @click="fileUploading = false">
                                <v-icon>mdi-arrow-left</v-icon>
                            </v-btn>

                            <v-btn v-else key="upload" outlined color="primary" @click="fileUploading = true">
                                <v-icon left dark>
                                    mdi-upload
                                </v-icon>
                                Завантажити файл
                            </v-btn>
                        </v-card-title>

                        <v-divider/>
                    </template>

                    <v-card-text>
                        <DamageForm
                            ref="damageForm"
                            :file-uploading="fileUploading"
                            :object-type-items="objectTypeItems"
                            :community-items="communityItems"
                            @submit-file="submitFile"
                            @submit-form="submitForm"
                        />
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
import DamageForm from '@/js/components/DamageForm';

export default {
    name: 'CreateDamageNote',
    components: {
        DamageForm,
    },

    data() {
        return {
            objectTypesLoading: false,
            communitiesLoading: false,
            objectTypeItems: [],
            communityItems: [],
            snackbarSuccess: false,
            snackbarError: false,
            fileUploading: false,
        }
    },

    computed: {
        isAuthorized() {
            return this.$store.getters.isAuthorized;
        },
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

        submitFile(data) {
            this.$refs.damageForm.formLoading = true;
            this.$store.dispatch('saveDamageNotesFromFile', { data })
                .then(() => {
                    this.$refs.damageForm.clearFile();
                    this.snackbarSuccess = true;
                })
                .catch(() => {
                    this.snackbarError = true;
                })
                .finally(() => {
                    this.$refs.damageForm.formLoading = false;
                });
        },

        submitForm(data) {
            const formattedData = {
                date: data.date,
                object_type_id: data.objectType,
                community_id: data.community,
                city: data.city,
                street: data.street,
                building_number: data.buildingNumber,
                damage_type: data.damageType,
                restoration_cost: data.restorationСost,
            };

            this.$refs.damageForm.formLoading = true;
            this.$store.dispatch('saveDamageNotes', { data: formattedData })
                .then(() => {
                    this.$refs.damageForm.clearForm();
                    this.snackbarSuccess = true;
                })
                .catch(() => {
                    this.snackbarError = true;
                })
                .finally(() => {
                    this.$refs.damageForm.formLoading = false;
                });
        },
    }
}
</script>

<style lang="scss" scoped>
.damage-note-card {
    width: 100%;
    max-width: 400px;
    margin: 0 auto;
}
</style>

