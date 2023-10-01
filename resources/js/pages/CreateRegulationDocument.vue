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
                    <v-card-text>
                        <RegulationDocumentForm
                            ref="regulationDocumentForm"
                            @submit-form="submitForm"
                        />
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
import RegulationDocumentForm from '@/js/components/RegulationDocumentForm';

export default {
    name: 'CreateRegulationDocument',
    components: {
        RegulationDocumentForm,
    },

    data() {
        return {
            snackbarSuccess: false,
            snackbarError: false,
        }
    },

    mounted() {
        //
    },

    methods: {
        submitForm(data) {
            this.$refs.regulationDocumentForm.formLoading = true;
            this.$store.dispatch('saveRegulationDocument', { data })
                .then(() => {
                    this.$refs.regulationDocumentForm.clearForm();
                    this.snackbarSuccess = true;
                })
                .catch(() => {
                    this.snackbarError = true;
                })
                .finally(() => {
                    this.$refs.regulationDocumentForm.formLoading = false;
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

