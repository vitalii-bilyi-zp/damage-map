<template>
    <v-card class="statistic-card">
        <v-toolbar flat color="white" height="64">
            <v-select
                v-model="objectCategory"
                :items="objectCategoryItemsComputed"
                dense
                outlined
                hide-details
                item-text="name"
                item-value="id"
                placeholder="Категорія об’єкта"
                :disabled="!objectCategoryItemsComputed || !objectCategoryItemsComputed.length"
            ></v-select>
        </v-toolbar>

        <v-divider></v-divider>

        <v-card-text>
            <div class="statistic-card__chart-wrapper">
                <div v-if="isLoading" class="card-progress">
                    <v-progress-circular :size="50" color="primary" indeterminate />
                </div>
                <DoughnutChart v-else :chart-data="ratioData" :options="ratioOptions" class="statistic-card__chart" />
            </div>
        </v-card-text>
    </v-card>
</template>

<script>
import DoughnutChart from '@/js/components/charts/DoughnutChart.vue';

const COLORS = [
    '#0aadd0',
    '#e43e6e',
    '#01c498',
    '#ffbe07',
    '#4e40de',
    '#7f2084',
];

export default {
    components: {
        DoughnutChart,
    },

    data() {
        return {
            isLoading: false,
            ratioData: {
                labels: [],
                datasets: []
            },
            ratioOptions: {
                responsive: true,
                maintainAspectRatio: false
            },
            objectTypesLoading: false,
            objectTypeItems: [],
            objectCategory: null,

            // type: null,
            // TypeSelectOptions: [
            //     {
            //         name: "Возраст",
            //         value: 0
            //     },
            //     {
            //         name: "Пол",
            //         value: 1
            //     }
            // ]
        };
    },

    computed: {
        objectCategoryItemsComputed() {
            const objectCategories = this.objectTypeItems.reduce((prev, curr) => {
                if (curr.object_category && !prev[curr.object_category.id]) {
                    prev[curr.object_category.id] = curr.object_category;
                }

                return prev;
            }, {})

            return Object.values(objectCategories);
        },
    },

    mounted() {
        this.loadObjectTypes();
    },

    watch: {
        type(value) {
            this.loadRatio(value);
        }
    },

    methods: {
        loadObjectTypes() {
            this.objectTypesLoading = true;
            this.$store.dispatch('loadObjectTypes')
                .then((response) => {
                    this.objectTypeItems = response.data || [];
                })
                .catch(() => {
                    //
                })
                .finally(() => {
                    this.objectTypesLoading = false;
                });
        },

        // loadRatio(type) {
        //     this.isLoading = true;
        //     this.$store
        //         .dispatch("loadRatioStatistics", {
        //             params: { type }
        //         })
        //         .then(() => {
        //             let chartData = this.$store.state.statistics.ratio;

        //             if (chartData) {
        //                 this.setRatio(chartData);
        //             }
        //         })
        //         .finally(() => {
        //             this.isLoading = false;
        //         });
        // },

        // setRatio(data) {
        //     let labels = [];
        //     let datasets = [
        //         {
        //         backgroundColor: [],
        //         data: []
        //         }
        //     ];

        //     Object.keys(data).forEach((key, index) => {
        //         labels.push(key);
        //         datasets[0].backgroundColor.push(COLORS[index]);
        //         datasets[0].data.push(data[key]);
        //     });

        //     this.ratioData = Object.assign({}, this.ratioData, {
        //         labels,
        //         datasets
        //     });
        // }
    }
};
</script>
