<template>
    <div class="damage-statistics">
        <v-container fluid>
            <v-row class="mx-0 mb-8" justify="center">
                <v-col cols="12" class="py-0">
                    <StatisticsTopLevelFilters />
                </v-col>
            </v-row>
            <v-row class="ma-0" justify="center">
                <v-col cols="12" md="6" class="py-0 mb-8 mb-md-0">
                    <GlobalStatistics />
                </v-col>
                <v-col cols="12" md="6" class="py-0">
                    <RatioStatistics />
                </v-col>
            </v-row>
        </v-container>
    </div>
</template>

<script>
import StatisticsTopLevelFilters from "@/js/components/statistics/StatisticsTopLevelFilters.vue";
import RatioStatistics from "@/js/components/statistics/RatioStatistics.vue";
import GlobalStatistics from "@/js/components/statistics/GlobalStatistics.vue";

export default {
    components: {
        StatisticsTopLevelFilters,
        RatioStatistics,
        GlobalStatistics,
    }
};
</script>

<style lang="scss" scoped>
.damage-statistics {
    height: 100%;
    padding: 16px;
}
</style>
