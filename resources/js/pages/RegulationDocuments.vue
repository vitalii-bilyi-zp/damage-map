<template>
    <v-container fluid>
        <v-row class="ma-0" justify="center">
            <v-col cols="12" class="py-0">
                <v-card>
                    <v-toolbar flat color="white">
                        <v-spacer></v-spacer>
                        <v-btn color="primary" link :to="{ name: 'regulation-documents.create' }">
                            <v-icon left>mdi-plus</v-icon>
                            Завантажити документ
                        </v-btn>
                    </v-toolbar>
                    <v-divider></v-divider>
                    <v-card-text class="pa-0">
                        <v-data-table
                            :headers="headers"
                            :items="regulationDocuments"
                            :loading="isLoading || isDeleting"
                            :items-per-page="15"
                            :footer-props="footerProps"
                            no-data-text="Інформація відсутня"
                            loading-text="Завантаження інформації..."
                            class="card-table elevation-1"
                            :mobile-breakpoint="0"
                        >
                            <template v-slot:item.created_at="{ item }">
                                {{ formatDate(item.created_at) }}
                            </template>

                            <template v-slot:item.actions="{ item }">
                                <v-icon
                                    small
                                    class="mr-2"
                                    @click="showRegulationDocument(item.file_path)"
                                >
                                    mdi-eye
                                </v-icon>
                                <v-icon
                                    small
                                    @click="confirmDeletion(item.id)"
                                >
                                    mdi-delete
                                </v-icon>
                            </template>

                            <template v-slot:footer.page-text="props">
                                {{props.pageStart}} - {{props.pageStop}} з {{props.itemsLength}}
                            </template>
                        </v-data-table>
                    </v-card-text>
                </v-card>

                <ConfirmDialog ref="confirmDialog"/>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
    import ConfirmDialog from '@/js/components/ConfirmDialog';

    import moment from 'moment';

    export default {
        name: 'RegulationDocuments',

        components: {
            ConfirmDialog,
        },

        data() {
            return {
                isLoading: false,
                isDeleting: false,
                regulationDocuments: [],
                headers: [
                    {
                        text: 'ID',
                        align: 'left',
                        sortable: true,
                        value: 'id',
                        width: 100,
                    },
                    {
                        text: 'Назва',
                        align: 'left',
                        sortable: true,
                        value: 'file_name',
                    },
                    {
                        text: 'Дата завантаження',
                        align: 'left',
                        sortable: true,
                        value: 'created_at',
                    },
                    {
                        text: 'Розмір',
                        align: 'left',
                        sortable: true,
                        value: 'size',
                    },
                    {
                        text: 'Формат',
                        align: 'left',
                        sortable: true,
                        value: 'extension',
                    },
                    {
                        text: '',
                        align: 'left',
                        sortable: false,
                        value: 'actions',
                        width: 80,
                    },
                ],
                footerProps: {
                    'items-per-page-options': [15, 30, 45],
                    'items-per-page-text': 'Елементів на сторінці:'
                },
            }
        },

        mounted() {
            this.loadRegulationDocuments();
        },

        methods: {
            loadRegulationDocuments() {
                this.isLoading = true;
                this.$store.dispatch('loadRegulationDocuments')
                    .then((response) => {
                        this.regulationDocuments = response.data || [];
                    })
                    .catch(() => {
                        //
                    })
                    .finally(() => {
                        this.isLoading = false;
                    });
            },

            formatDate(date) {
                return moment(date).format('YYYY-MM-DD');
            },

            showRegulationDocument(url) {
                window.open(url, '_blank').focus();
            },

            confirmDeletion(id) {
                this.$refs.confirmDialog.open('Підтвердження операції', 'Ви впевнені, що хочете видалити цей документ?')
                    .then((isConfirmed) => {
                        if (!isConfirmed) {
                            return;
                        }

                        this.deleteRegulationDocument(id);
                    })
            },

            deleteRegulationDocument(id) {
                this.isDeleting = true;
                this.$store.dispatch('deleteRegulationDocument', id)
                    .then(() => {
                        this.loadRegulationDocuments();
                    })
                    .catch(() => {
                        //
                    })
                    .finally(() => {
                        this.isDeleting = false;
                    });
            },
        }
    }
</script>

<style lang="scss" scoped>
    .card-table::v-deep {
        table {
            table-layout: fixed;
            min-width: 1265px;
        }
    }
</style>
