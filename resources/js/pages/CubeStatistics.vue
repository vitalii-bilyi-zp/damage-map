<template>
    <div class="cube-statistics">
        <v-container fluid>
            <v-row class="mx-0 mb-8" justify="center">
                <v-col cols="12" class="py-0">
                    <CubeStatisticsTopLevelFilters />
                </v-col>
            </v-row>
            <v-row class="ma-0" justify="center">
                <v-col cols="12" class="py-0 mb-8 mb-md-0">
                    <Cube />
                </v-col>
            </v-row>
        </v-container>
    </div>
</template>

<script>
import CubeStatisticsTopLevelFilters from "@/js/components/cube-statistics/CubeStatisticsTopLevelFilters.vue";
import Cube from "@/js/components/cube-statistics/Cube.vue";

export default {
    components: {
        CubeStatisticsTopLevelFilters,
        Cube
    }
};
</script>

<style lang="scss" scoped>
.cube-statistics {
    height: 100%;
    padding: 16px;
}
</style>
