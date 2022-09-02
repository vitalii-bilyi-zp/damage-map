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

                <template v-if="isAuthorized">
                    <v-list-item link :to="{name: 'damage-notes'}" exact>
                        <v-list-item-icon>
                            <v-icon>mdi-view-list</v-icon>
                        </v-list-item-icon>

                        <v-list-item-content>
                            <v-list-item-title>Дані пошкоджень</v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>

                    <v-divider class="mb-2"></v-divider>

                    <v-list-item link @click="logout">
                        <v-list-item-icon>
                            <v-icon>mdi-logout-variant</v-icon>
                        </v-list-item-icon>

                        <v-list-item-content>
                            <v-list-item-title>Вийти</v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                </template>
                <template v-else>
                    <v-list-item link :to="{name: 'damage-notes.create'}" exact>
                        <v-list-item-icon>
                            <v-icon>mdi-plus</v-icon>
                        </v-list-item-icon>

                        <v-list-item-content>
                            <v-list-item-title>Додати запис</v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                </template>
            </v-list>
        </v-navigation-drawer>

        <v-app-bar app>
            <v-app-bar-nav-icon @click="toggleDrawer"/>
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
            }
        },

        computed: {
            isAuthorized() {
                return this.$store.getters.isAuthorized;
            },
        },

        methods: {
            toggleDrawer() {
                this.drawerState = !this.drawerState;
            },

            logout() {
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
