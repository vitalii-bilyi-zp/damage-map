<template>
    <v-card class="statistic-card">
        <v-toolbar flat color="white" height="64">
            <v-select
                v-model="periodType"
                :items="periodTypeItems"
                dense
                outlined
                hide-details
                item-text="name"
                item-value="id"
                placeholder="Тип періоду"
            ></v-select>
        </v-toolbar>

        <v-divider></v-divider>

        <v-card-text>
            <div class="statistic-card__chart-wrapper">
                <div v-if="isLoading" class="card-progress">
                    <v-progress-circular :size="50" color="primary" indeterminate />
                </div>
                <BarChart v-else :chart-data="chartData" :options="chartOptions" class="statistic-card__chart" />
            </div>
        </v-card-text>
    </v-card>
</template>

<script>
import BarChart from '@/js/components/charts/BarChart.vue';

import moment from 'moment';

export default {
    name: 'GlobalStatistics',
    components: {
        BarChart,
    },

    data() {
        return {
            periodType: null,
            periodTypeItems: [
                {
                    id: 0,
                    name: "День",
                },
                {
                    id: 1,
                    name: "Тиждень",
                },
                {
                    id: 2,
                    name: "Місяць",
                },
            ],

            isLoading: false,
            chartData: {
                labels: [],
                datasets: []
            },
            chartOptions: {
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                    duration: 0
                },
                hover: {
                    animationDuration: 0
                },
                responsiveAnimationDuration: 0
            },
        };
    },

    computed: {
        filters() {
            return this.$store.state.filters;
        },
        chartFilters() {
            return {
                ...this.filters,
                periodType: this.periodType
            };
        }
    },

    mounted() {
        this.initPeriodType();
    },

    watch: {
        chartFilters: {
            handler(newValue) {
                let date = newValue.period;

                if (!date || date.length < 2) {
                    date = [
                        moment().startOf('month').format('YYYY-MM-DD'),
                        moment().format('YYYY-MM-DD')
                    ];
                }

                let formattedFilters = {
                    start_date: date[0],
                    end_date: date[1],
                    region_id: newValue.region,
                    period_type: newValue.periodType,
                };

                this.loadChartData(formattedFilters);
            },
            deep: true
        }
    },

    methods: {
        initPeriodType() {
            this.periodType = this.periodTypeItems[0].id;
        },

        loadChartData(filters) {
            this.isLoading = true;
            this.$store.dispatch('loadGlobalStatistics', {
                params: filters
            })
            .then((response) => {
                let chartData = response.data;

                if (chartData) {
                    this.setChartData(chartData);
                }
            })
            .catch(() => {
                //
            })
            .finally(() => {
                this.isLoading = false;
            });
        },

        setChartData(data) {
            let color = "#f8a421";
            let label = "Вартість відновлення";
            let dataq = {
                label: label,
                backgroundColor: color,
                borderColor: color,
                data: data ? Object.values(data) : [],
                fill: false,
                spanGaps: true
            };

            this.chartData = Object.assign({}, this.chartData, {
                labels: Object.keys(data),
                datasets: [dataq]
            });
        }
    }
};
</script>
