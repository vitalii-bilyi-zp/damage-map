<template>
    <router-view></router-view>
</template>

<script>
    export default {
        name: 'App',
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

