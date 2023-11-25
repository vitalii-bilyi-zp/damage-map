<template>
    <v-container fluid>
        <v-row class="ma-0" justify="center">
            <v-col cols="12" class="py-0">
                <v-card>
                    <v-toolbar flat color="white">
                        <v-spacer></v-spacer>
                        <v-btn color="primary" link :to="{ name: 'users.create' }">
                            <v-icon left>mdi-plus</v-icon>
                            Додати
                        </v-btn>
                    </v-toolbar>
                    <v-divider></v-divider>
                    <v-card-text class="pa-0">
                        <v-data-table
                            :headers="headers"
                            :items="users"
                            :loading="isLoading || isDeleting"
                            :items-per-page="15"
                            :footer-props="footerProps"
                            no-data-text="Інформація відсутня"
                            loading-text="Завантаження інформації..."
                            class="card-table elevation-1"
                            :mobile-breakpoint="0"
                        >
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
                                                :key="index"
                                                ripple="ripple"
                                                @click="menuItem.click ? menuItem.click(item.id) : null"
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
                                {{ props.pageStart }} - {{ props.pageStop }} з {{ props.itemsLength }}
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

    export default {
        name: "Users",

        components: {
            ConfirmDialog,
        },

        data() {
            return {
                isLoading: false,
                isDeleting: false,
                users: [],
                headers: [
                    {
                        text: 'ID',
                        align: 'left',
                        sortable: true,
                        value: 'id',
                        width: 100,
                    },
                    {
                        text: 'ПІБ',
                        align: 'left',
                        sortable: true,
                        value: 'name',
                    },
                    {
                        text: 'E-mail',
                        align: 'left',
                        sortable: true,
                        value: 'email',
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
                        click: this.updateUser,
                        title: "Редагувати",
                    },

                    { divider: true },

                    {
                        icon: "mdi-delete",
                        click: this.confirmDeletion,
                        title: "Видалити",
                    },
                ],
            }
        },

        mounted() {
            this.loadUsers();
        },

        watch: {
            options: {
                handler() {
                    this.loadUsers();
                },
                deep: true,
            },
            search() {
                this.options.page = 1;
                this.loadUsers();
            }
        },

        methods: {
            loadUsers() {
                this.isLoading = true;
                this.$store.dispatch('loadUsers')
                    .then((response) => {
                        this.users = response.data || [];
                    })
                    .catch(() => {
                        //
                    })
                    .finally(() => {
                        this.isLoading = false;
                    });
            },

            updateUser(id) {
                this.$router.push({name: 'users.edit', params: { id }});
            },

            confirmDeletion(id) {
                this.$refs.confirmDialog.open('Підтвердження операції', 'Ви впевнені, що хочете видалити цього користувача?')
                    .then((isConfirmed) => {
                        if (!isConfirmed) {
                            return;
                        }

                        this.deleteUser(id);
                    })
            },

            deleteUser(id) {
                this.$store.dispatch('deleteUser', id)
                    .then(() => {
                        this.loadUsers();
                    });
            },
        }
    }
</script>

<style scoped>
    .text--crossed {
        text-decoration: line-through;
    }
</style>
