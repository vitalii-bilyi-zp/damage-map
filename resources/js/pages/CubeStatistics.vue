<template>
    <v-container fluid>
        <v-row class="ma-0" justify="center">
            <v-col cols="12" class="py-0">
                <v-card>
                    <v-toolbar flat color="white">
                        <CubeStatisticsTopLevelFilters />
                    </v-toolbar>

                    <v-divider></v-divider>

                    <v-card-text class="pa-0">
                        <CubeStatisticsTable />
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
import CubeStatisticsTopLevelFilters from "@/js/components/cube-statistics/CubeStatisticsTopLevelFilters.vue";
import CubeStatisticsTable from "@/js/components/cube-statistics/CubeStatisticsTable.vue";

export default {
    components: {
        CubeStatisticsTopLevelFilters,
        CubeStatisticsTable
    }
};
</script>
