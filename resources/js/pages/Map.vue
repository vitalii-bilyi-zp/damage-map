<template>
    <div class="map">
        <div v-if="isLoading" class="map__loader">
            <v-progress-circular
                color="#757575"
                :size="60"
                :width="3"
                indeterminate
            />
        </div>

    </div>
</template>

<script>
    export default {
        name: 'Map',

        data() {
            return {
                isLoading: false,
                dataLoaded: false,
                regionsData: [],
                districtsData: [],
                communitiesData: [],
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
                    .then(([regionsResponse, districtsResponse, communitiesResponse]) => {
                        this.regionsData = regionsResponse.data || [];
                        this.districtsData = districtsResponse.data || [];
                        this.communitiesData = communitiesResponse.data || [];

                        if (!this.dataLoaded) {
                            this.dataLoaded = true;
                        }
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
.map {
    position: relative;
    height: 100%;
}

.map__loader {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>

