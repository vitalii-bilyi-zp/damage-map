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

                <v-list-item link :to="{name: 'damage-notes'}" exact>
                    <v-list-item-icon>
                        <v-icon>mdi-view-list</v-icon>
                    </v-list-item-icon>

                    <v-list-item-content>
                        <v-list-item-title>Записи пошкоджень</v-list-item-title>
                    </v-list-item-content>
                </v-list-item>
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
        name: 'App',

        data() {
            return {
                drawerState: false,
            }
        },

        methods: {
            toggleDrawer() {
                this.drawerState = !this.drawerState;
            },
        }
    }
</script>

<style lang="scss">
    .theme--light {
        .v-app-bar.v-toolbar.v-sheet {
            background-color: #fff;
        }

        .v-main {
            background-color: #f5f5f5;
        }
    }
</style>

