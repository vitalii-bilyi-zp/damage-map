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

                <v-card color="damage-note-card">
                    <v-card-text>
                        <div v-if="isLoading || objectTypesLoading || communitiesLoading" class="card-progress">
                            <v-progress-circular :size="50" color="primary" indeterminate />
                        </div>

                        <DamageForm
                            v-else
                            ref="damageForm"
                            :object-type-items="objectTypeItems"
                            :community-items="communityItems"
                            :damage-note="damageNote"
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

import moment from 'moment';

export default {
    name: 'EditDamageNote',
    components: {
        DamageForm,
    },

    props: ['id'],

    data() {
        return {
            isLoading: false,
            objectTypesLoading: false,
            communitiesLoading: false,
            objectTypeItems: [],
            communityItems: [],
            damageNote: null,
            snackbarSuccess: false,
            snackbarError: false,
        }
    },

    mounted() {
        this.loadDamageNote();
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

        loadDamageNote() {
            this.isLoading = true;
            this.$store.dispatch('loadDamageNote', this.id)
                .then((response) => {
                    this.damageNote = this.formatDamageNote(response.data);
                })
                .catch(() => {
                    //
                })
                .finally(() => {
                    this.isLoading = false;
                });
        },

        formatDamageNote(data) {
            if (!data) {
                return null;
            }

            return {
                date: data.date && moment(data.date).format('YYYY-MM-DD'),
                objectCategory: data.object_type && data.object_type.object_category_id,
                objectType: data.object_type_id,
                community: data.community_id,
                city: data.city,
                street: data.street,
                buildingNumber: data.building_number,
                damageType: data.damage_type,
                restorationСost: data.restoration_cost,
                comment: data.comment,
            }
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
                comment: data.comment,
            };

            this.$refs.damageForm.formLoading = true;
            this.$store.dispatch('updateDamageNote', { id: this.id, data: formattedData })
                .then(() => {
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

