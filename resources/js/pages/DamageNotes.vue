<template>
    <v-container fluid>
        <v-row class="ma-0" justify="center">
            <v-col cols="12" class="py-0">
                <v-card>
                    <v-toolbar flat color="white">
                        <v-spacer></v-spacer>
                        <v-btn color="primary" link :to="{ name: 'create-damage-note' }">
                            <v-icon left>mdi-plus</v-icon>
                            Додати запис
                        </v-btn>
                    </v-toolbar>
                    <v-divider></v-divider>
                    <v-card-text class="pa-0">
                        <v-data-table
                            :headers="headers"
                            :items="damageNotes"
                            :loading="isLoading || isDeleting"
                            :items-per-page="15"
                            :footer-props="footerProps"
                            no-data-text="Інформація відсутня"
                            loading-text="Завантаження інформації..."
                            class="card-table elevation-1"
                            :mobile-breakpoint="0"
                        >
                            <template v-slot:item.date="{ item }">
                                {{ formatDate(item.date) }}
                            </template>

                            <template v-slot:item.damage_type="{ item }">
                                {{ formatDamageType(item.damage_type) }}
                            </template>

                            <template v-slot:item.actions="{ item }">
                                <v-menu left offset-y origin="center center" :nudge-bottom="10" transition="scale-transition">
                                    <template v-slot:activator="{ on }">
                                        <v-btn text icon v-on="on">
                                            <v-icon>mdi-dots-vertical</v-icon>
                                        </v-btn>
                                    </template>

                                    <v-list class="pa-0">
                                        <template v-for="(menuItem, index) in tableActions">
                                            <v-divider
                                                v-if="menuItem.divider"
                                                :key="index"
                                            ></v-divider>
                                            <v-list-item
                                                v-else
                                                @click="menuItem.click ? menuItem.click(item.id) : null"
                                                :key="index"
                                                ripple="ripple"
                                            >
                                                <v-list-item-icon v-if="menuItem.icon" class="mr-4">
                                                    <v-icon>{{ menuItem.icon }}</v-icon>
                                                </v-list-item-icon>
                                                <v-list-item-content>
                                                    <v-list-item-title>{{ menuItem.title }}</v-list-item-title>
                                                </v-list-item-content>
                                            </v-list-item>
                                        </template>
                                    </v-list>
                                </v-menu>
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
        name: 'DamageNotes',

        components: {
            ConfirmDialog,
        },

        data() {
            return {
                isLoading: false,
                isDeleting: false,
                damageNotes: [],
                headers: [
                    {
                        text: 'ID',
                        align: 'left',
                        sortable: true,
                        value: 'id',
                        width: 100,
                    },
                    {
                        text: 'Дата',
                        align: 'left',
                        sortable: true,
                        value: 'date',
                    },
                    {
                        text: 'Тип об’єкта',
                        align: 'left',
                        sortable: true,
                        value: 'object_type',
                    },
                    {
                        text: 'Територіальна громада',
                        align: 'left',
                        sortable: true,
                        value: 'community',
                    },
                    {
                        text: 'Місто / селище',
                        align: 'left',
                        sortable: true,
                        value: 'city',
                    },
                    {
                        text: 'Вулиця',
                        align: 'left',
                        sortable: true,
                        value: 'street',
                    },
                    {
                        text: 'Будівля',
                        align: 'left',
                        sortable: true,
                        value: 'building_number',
                    },
                    {
                        text: 'Тип пошкодження',
                        align: 'left',
                        sortable: true,
                        value: 'damage_type',
                    },
                    {
                        text: 'Вартість відновлення',
                        align: 'left',
                        sortable: true,
                        value: 'restoration_cost',
                    },
                    {
                        text: '',
                        align: 'left',
                        sortable: false,
                        value: 'actions',
                        width: 70,
                    },
                ],
                footerProps: {
                    'items-per-page-options': [15, 30, 45],
                    'items-per-page-text': 'Елементів на сторінці:'
                },
                tableActions: [
                    {
                        icon: "mdi-pencil",
                        click: this.updateDamageNote,
                        title: "Редагувати запис",
                    },

                    { divider: true },

                    {
                        icon: "mdi-delete",
                        click: this.confirmDeletion,
                        title: "Видалити запис",
                    },
                ],
                damageTypeItems: [
                    {
                        id: 'high',
                        name: 'Повне руйнування',
                    },
                    {
                        id: 'medium',
                        name: 'Сильне руйнування',
                    },
                    {
                        id: 'low',
                        name: 'Слабке руйнування',
                    }
                ],
            }
        },

        mounted() {
            this.loadDamageNotes();
        },

        methods: {
            loadDamageNotes() {
                this.isLoading = true;
                this.$store.dispatch('loadDamageNotes')
                    .then((response) => {
                        this.damageNotes = response.data || [];
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

            formatDamageType(type) {
                const foundItem = this.damageTypeItems.find((item) => item.id === type);
                return foundItem ? foundItem.name || type : type;
            },

            confirmDeletion(id) {
                this.$refs.confirmDialog.open('Підтвердження операції', 'Ви впевнені, що хочете видалити цей запис?')
                    .then((isConfirmed) => {
                        if (!isConfirmed) {
                            return;
                        }

                        this.deleteDamageNote(id);
                    })
            },

            deleteDamageNote(id) {
                this.isDeleting = true;
                this.$store.dispatch('deleteDamageNote', id)
                    .then(() => {
                        this.loadDamageNotes();
                    })
                    .catch(() => {
                        //
                    })
                    .finally(() => {
                        this.isDeleting = false;
                    });
            },

            updateDamageNote(id) {
                this.$router.push({name: 'edit-damage-note', params: { id }});
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
