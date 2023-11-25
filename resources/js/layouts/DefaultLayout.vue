<template>
    <v-app id="inspire">
        <v-navigation-drawer app v-model="drawerState">
            <v-list nav>
                <v-list-item link :to="{name: 'map'}" exact>
                    <v-list-item-icon>
                        <v-icon>mdi-map</v-icon>
                    </v-list-item-icon>

                    <v-list-item-content>
                        <v-list-item-title>Мапа</v-list-item-title>
                    </v-list-item-content>
                </v-list-item>

                <v-list-item link :to="{name: 'statistics'}" exact>
                    <v-list-item-icon>
                        <v-icon>mdi-chart-areaspline</v-icon>
                    </v-list-item-icon>

                    <v-list-item-content>
                        <v-list-item-title>Статистика</v-list-item-title>
                    </v-list-item-content>
                </v-list-item>

                <v-list-item link :to="{name: 'regulation-documents'}" exact>
                    <v-list-item-icon>
                        <v-icon>mdi-file-multiple</v-icon>
                    </v-list-item-icon>

                    <v-list-item-content>
                        <v-list-item-title>Нормативні акти</v-list-item-title>
                    </v-list-item-content>
                </v-list-item>

                <template v-if="isAuthorized">
                    <v-list-item link :to="{name: 'damage-notes'}" exact>
                        <v-list-item-icon>
                            <v-icon>mdi-view-list</v-icon>
                        </v-list-item-icon>

                        <v-list-item-content>
                            <v-list-item-title>Дані пошкоджень</v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>

                    <v-list-item v-if="isSuperAdmin" link :to="{name: 'users'}" exact>
                        <v-list-item-icon>
                            <v-icon>mdi-account-group</v-icon>
                        </v-list-item-icon>

                        <v-list-item-content>
                            <v-list-item-title>Користувачі</v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                </template>
            </v-list>
        </v-navigation-drawer>

        <v-app-bar app>
            <v-app-bar-nav-icon @click="toggleDrawer"/>

            <v-spacer></v-spacer>

            <v-toolbar-items>
                <v-menu v-if="user" offset-y origin="center center" :nudge-bottom="10" transition="scale-transition">
                    <template v-slot:activator="{ on }">
                        <v-btn text large v-on="on">
                            {{ (user && user.name) ? user.name : 'unknown user' }}
                            <v-icon class="ml-2">mdi-chevron-down</v-icon>
                        </v-btn>
                    </template>

                    <v-list class="pa-0">
                        <template v-for="(item, index) in accountMenu">
                            <v-list-item
                                :key="`item-${index}`"
                                ripple="ripple"
                                :to="!item.href ? { name: item.name } : null"
                                :href="item.href"
                                @click.prevent="item.click"
                                :disabled="item.disabled"
                            >
                                <v-list-item-content>
                                    <v-list-item-title>{{ item.title }}</v-list-item-title>
                                </v-list-item-content>
                                <v-list-item-icon v-if="item.icon">
                                    <v-icon>{{ item.icon }}</v-icon>
                                </v-list-item-icon>
                            </v-list-item>

                            <v-divider v-if="index + 1 < accountMenu.length" :key="`divider-${index}`"></v-divider>
                        </template>
                    </v-list>
                </v-menu>
                <v-btn v-else text large :to="{name: 'auth.login'}">
                    Увійти
                    <v-icon class="ml-2">mdi-login</v-icon>
                </v-btn>
            </v-toolbar-items>
        </v-app-bar>

        <v-main>
            <router-view></router-view>
        </v-main>
    </v-app>
</template>

<script>
    export default {
        name: 'DefaultLayout',

        data() {
            return {
                drawerState: false,
                accountMenu: [
                    {
                        icon: "mdi-pencil",
                        href: "#",
                        title: 'Редагувати',
                        click: this.handleEdit
                    },
                    {
                        icon: "mdi-logout",
                        href: "#",
                        title: 'Вийти',
                        click: this.handleLogout
                    }
                ]
            }
        },

        computed: {
            isAuthorized() {
                return this.$store.getters.isAuthorized;
            },

            isSuperAdmin() {
                return this.$store.getters.isSuperAdmin;
            },

            user() {
                return this.$store.state.currentUser;
            }
        },

        methods: {
            toggleDrawer() {
                this.drawerState = !this.drawerState;
            },

            handleEdit() {
                if (this.user && this.user.id) {
                    this.$router.push({ name: 'users.edit', params: { id: this.user.id }});
                }
            },

            handleLogout() {
                this.$store.dispatch('logout')
                    .then(() => {
                        this.$router.push({ name: 'map' });
                    })
            },
        }
    }
</script>

<style lang="scss">

</style>
