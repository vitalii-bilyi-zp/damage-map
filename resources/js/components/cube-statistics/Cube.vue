<template>
    <v-card class="statistic-card">
        <v-card-text class="pa-0">
            <v-data-table
                :headers="headers"
                :items="items"
                :loading="isLoading"
                :items-per-page="15"
                :footer-props="footerProps"
                no-data-text="Інформація відсутня"
                loading-text="Завантаження інформації..."
                class="card-table elevation-1"
                :mobile-breakpoint="0"
            >
                <template v-slot:footer.page-text="props">
                    {{ props.pageStart }} - {{ props.pageStop }} з {{ props.itemsLength }}
                </template>
            </v-data-table>
        </v-card-text>
    </v-card>
</template>

<script>
import moment from 'moment';

export default {
    name: 'CubeStatisticsTable',

    data() {
        return {
            isLoading: false,
            items: [],
            headers: [
                {
                    text: 'Показник виміру',
                    align: 'left',
                    sortable: true,
                    value: 'title',
                },
                {
                    text: 'Кількість об\'єктів',
                    align: 'left',
                    sortable: true,
                    value: 'objects_number',
                },
                {
                    text: 'Вартість відновлення',
                    align: 'left',
                    sortable: true,
                    value: 'restoration_cost',
                },
            ],
            footerProps: {
                'items-per-page-options': [15, 30, 45],
                'items-per-page-text': 'Елементів на сторінці:'
            },
        };
    },

    computed: {
        filters() {
            return this.$store.state.cubeFilters;
        },
    },

    watch: {
        filters: {
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
                    dimension_type: newValue.dimensionType,
                };

                this.loadCubeData(formattedFilters);
            },
            deep: true
        }
    },

    mounted() {
        this.loadCubeData();
    },

    methods: {
        loadCubeData(filters) {
            this.isLoading = true;
            this.$store.dispatch('loadCubeStatistics', {
                params: filters
            })
            .then((response) => {
                this.items = response.data || [];
            })
            .catch(() => {
                //
            })
            .finally(() => {
                this.isLoading = false;
            });
        },
    }
};
</script>
