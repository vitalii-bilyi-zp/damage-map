<template>
    <v-card class="statistic-card">
        <v-toolbar flat color="white" height="64">
            <v-select
                v-model="objectCategory"
                :items="objectCategoryItems"
                dense
                outlined
                hide-details
                item-text="name"
                item-value="id"
                placeholder="Категорія об’єкта"
                :disabled="!objectCategoryItems || !objectCategoryItems.length"
            ></v-select>
        </v-toolbar>

        <v-divider></v-divider>

        <v-card-text>
            <div class="statistic-card__chart-wrapper">
                <div v-if="isLoading || objectCategoriesLoading" class="card-progress">
                    <v-progress-circular :size="50" color="primary" indeterminate />
                </div>
                <DoughnutChart v-else :chart-data="chartData" :options="chartOptions" class="statistic-card__chart" />
            </div>
        </v-card-text>
    </v-card>
</template>

<script>
import DoughnutChart from '@/js/components/charts/DoughnutChart.vue';

import moment from 'moment';

const COLORS = [
    '#0aadd0',
    '#e43e6e',
    '#01c498',
    '#ffbe07',
    '#4e40de',
    '#7f2084',
];

export default {
    name: 'RatioStatistics',
    components: {
        DoughnutChart,
    },

    data() {
        return {
            objectCategoriesLoading: false,
            objectCategoryItems: [],
            objectCategory: null,

            isLoading: false,
            chartData: {
                labels: [],
                datasets: []
            },
            chartOptions: {
                responsive: true,
                maintainAspectRatio: false
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
                objectCategory: this.objectCategory
            };
        }
    },

    mounted() {
        this.initObjectCategory();
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
                    dimension_type: newValue.dimensionType,
                    object_category_id: newValue.objectCategory,
                };

                this.loadChartData(formattedFilters);
            },
            deep: true
        }
    },

    methods: {
        initObjectCategory() {
            this.objectCategoriesLoading = true;
            this.$store.dispatch('loadObjectTypes')
                .then((response) => {
                    let objectTypeItems = response.data || [];
                    this.objectCategoryItems = this.getObjectCategories(objectTypeItems);
                    if (this.objectCategoryItems.length) {
                        this.objectCategory = this.objectCategoryItems[0].id;
                    }
                })
                .catch(() => {
                    //
                })
                .finally(() => {
                    this.objectCategoriesLoading = false;
                });
        },

        getObjectCategories(items) {
            if (!items) {
                return [];
            }

            const objectCategories = items.reduce((prev, curr) => {
                if (curr.object_category && !prev[curr.object_category.id]) {
                    prev[curr.object_category.id] = curr.object_category;
                }

                return prev;
            }, {});

            return Object.values(objectCategories);
        },

        loadChartData(filters) {
            this.isLoading = true;
            this.$store.dispatch('loadRatioStatistics', {
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
            let labels = [];
            let datasets = [{
                backgroundColor: [],
                data: []
            }];

            Object.keys(data).forEach((key, index) => {
                labels.push(key);
                datasets[0].backgroundColor.push(COLORS[index]);
                datasets[0].data.push(data[key]);
            });

            this.chartData = Object.assign({}, this.chartData, {
                labels,
                datasets
            });
        }
    }
};
</script>
