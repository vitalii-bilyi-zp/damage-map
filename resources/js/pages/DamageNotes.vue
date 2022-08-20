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
    </div>
</template>

<script>
import DamageForm from '@/js/components/DamageForm';

export default {
    name: 'DamageNotes',
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
            this.$store.dispatch('saveDamageNotesFromFile', data)
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
            this.$refs.damageForm.formLoading = true;
            this.$store.dispatch('saveDamageNotes', data)
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
</style>

