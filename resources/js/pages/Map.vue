<template>
    <div>
        {{isLoading}}
    </div>
</template>

<script>
    export default {
        name: 'Map',

        data() {
            return {
                isLoading: false,
            }
        },

        mounted() {
            this.loadMapData();
        },

        methods: {
            loadMapData() {
                const promises = [
                    this.$store.dispatch('loadRegionsData'),
                    this.$store.dispatch('loadDistrictsData'),
                    this.$store.dispatch('loadCommunitiesData'),
                ];

                this.isLoading = true;
                Promise.all(promises)
                    .then(() => {
                        //
                    })
                    .catch(() => {
                        //
                    })
                    .finally(() => {
                        this.isLoading = false;
                    });
            },
        },
    }
</script>

<style lang="scss" scoped>

</style>

