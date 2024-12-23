<template>
    <v-container fluid>
        <v-row class="ma-0">
            <v-col cols="12" class="py-0">
                <v-card>
                    <div v-if="isLoading" class="card-progress">
                        <v-progress-circular :size="50" color="primary" indeterminate />
                    </div>

                    <template v-else-if="damageNote">
                        <v-toolbar flat>
                            <v-toolbar-title>
                                {{ damageNote.street }} {{ damageNote.buildingNumber }}, {{ damageNote.city }}
                            </v-toolbar-title>
                        </v-toolbar>

                        <v-divider/>

                        <v-row class="ma-0">
                            <v-col cols="12" sm="4" class="damage-note-tabs__wrapper">
                                <v-tabs v-model="tab" vertical>
                                    <v-tab key="tab1" class="damage-note-tabs__tab">
                                        <v-icon left>
                                            mdi-view-list-outline
                                        </v-icon>
                                        Загальна інформація
                                    </v-tab>
                                    <v-tab key="tab2" class="damage-note-tabs__tab">
                                        <v-icon left>
                                            mdi-cash-multiple
                                        </v-icon>
                                        Фінансування
                                    </v-tab>
                                </v-tabs>
                            </v-col>

                            <v-col cols="12" sm="8">
                                <v-tabs-items v-model="tab">
                                    <v-tab-item key="tab1">
                                        <v-card flat>
                                            <v-card-text>
                                                <v-row>
                                                    <v-col cols="12" sm="4">
                                                        <v-list-item two-line>
                                                            <v-list-item-content>
                                                                <v-list-item-subtitle class="mb-1">Дата пошкодження</v-list-item-subtitle>
                                                                <v-list-item-title>{{ damageNote.date }}</v-list-item-title>
                                                            </v-list-item-content>
                                                        </v-list-item>
                                                    </v-col>
                                                    <v-col cols="12" sm="4">
                                                        <v-list-item two-line>
                                                            <v-list-item-content>
                                                                <v-list-item-subtitle class="mb-1">Тип пошкодження</v-list-item-subtitle>
                                                                <v-list-item-title>{{ damageTypeLabel }}</v-list-item-title>
                                                            </v-list-item-content>
                                                        </v-list-item>
                                                    </v-col>
                                                    <v-col cols="12" sm="4">
                                                        <v-list-item two-line>
                                                            <v-list-item-content>
                                                                <v-list-item-subtitle class="mb-1">Оціночна вартість відновлення</v-list-item-subtitle>
                                                                <v-list-item-title>{{ formatCurrency(damageNote.restorationСost) }}</v-list-item-title>
                                                            </v-list-item-content>
                                                        </v-list-item>
                                                    </v-col>
                                                </v-row>

                                                <v-divider/>

                                                <v-row>
                                                    <v-col cols="12" sm="4">
                                                        <v-list-item two-line>
                                                            <v-list-item-content>
                                                                <v-list-item-subtitle class="mb-1">Місто / селище</v-list-item-subtitle>
                                                                <v-list-item-title>{{ damageNote.city }}</v-list-item-title>
                                                            </v-list-item-content>
                                                        </v-list-item>
                                                    </v-col>
                                                    <v-col cols="12" sm="4">
                                                        <v-list-item two-line>
                                                            <v-list-item-content>
                                                                <v-list-item-subtitle class="mb-1">Вулиця</v-list-item-subtitle>
                                                                <v-list-item-title>{{ damageNote.street }}</v-list-item-title>
                                                            </v-list-item-content>
                                                        </v-list-item>
                                                    </v-col>
                                                    <v-col cols="12" sm="4">
                                                        <v-list-item two-line>
                                                            <v-list-item-content>
                                                                <v-list-item-subtitle class="mb-1">Будівля</v-list-item-subtitle>
                                                                <v-list-item-title>{{ damageNote.buildingNumber }}</v-list-item-title>
                                                            </v-list-item-content>
                                                        </v-list-item>
                                                    </v-col>
                                                </v-row>

                                                <v-divider/>

                                                <v-row>
                                                    <v-col cols="12" sm="4">
                                                        <v-list-item two-line>
                                                            <v-list-item-content>
                                                                <v-list-item-subtitle class="mb-1">Категорія об’єкта</v-list-item-subtitle>
                                                                <v-list-item-title>{{ objectCategoryLabel }}</v-list-item-title>
                                                            </v-list-item-content>
                                                        </v-list-item>
                                                    </v-col>
                                                    <v-col cols="12" sm="4">
                                                        <v-list-item two-line>
                                                            <v-list-item-content>
                                                                <v-list-item-subtitle class="mb-1">Тип об’єкта</v-list-item-subtitle>
                                                                <v-list-item-title>{{ objectTypeLabel }}</v-list-item-title>
                                                            </v-list-item-content>
                                                        </v-list-item>
                                                    </v-col>
                                                </v-row>
                                            </v-card-text>
                                        </v-card>
                                    </v-tab-item>

                                    <v-tab-item key="tab2">
                                        <v-card flat>
                                            <v-card-text>
                                                <v-row>
                                                    <v-col cols="12" sm="6">
                                                        <v-list-item two-line class="mb-4">
                                                            <v-list-item-content class="damage-note-funds">
                                                                <v-list-item-subtitle class="damage-note-funds__title mb-3">Оціночна вартість відновлення</v-list-item-subtitle>
                                                                <v-list-item-title class="damage-note-funds__value">{{ formatCurrency(damageNote.restorationСost) }}</v-list-item-title>
                                                            </v-list-item-content>
                                                        </v-list-item>

                                                        <v-list-item two-line>
                                                            <v-list-item-content class="damage-note-funds">
                                                                <v-list-item-subtitle class="damage-note-funds__title mb-3">Загальний обсяг підтверджених джерел фінансування</v-list-item-subtitle>
                                                                <v-list-item-title class="damage-note-funds__value">{{ formatCurrency(mockedRestorationFunds) }}</v-list-item-title>
                                                            </v-list-item-content>
                                                        </v-list-item>
                                                    </v-col>
                                                    <v-col cols="12" sm="6">
                                                        <v-list-item two-line>
                                                            <v-list-item-content>
                                                                <v-list-item-subtitle class="mb-3 text-center">Фінансове покриття</v-list-item-subtitle>
                                                                <div class="damage-note-doughnut__wrapper">
                                                                    <DoughnutChart :chart-data="chartData" :options="chartOptions" class="damage-note-doughnut" />
                                                                    <span class="damage-note-doughnut__percent">
                                                                        {{ chartData.datasets.length ? chartData.datasets[0].data[0] : 0 }} %
                                                                    </span>
                                                                </div>
                                                            </v-list-item-content>
                                                        </v-list-item>
                                                    </v-col>
                                                </v-row>
                                            </v-card-text>
                                        </v-card>
                                    </v-tab-item>
                                </v-tabs-items>
                            </v-col>
                        </v-row>
                    </template>
                </v-card>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
import DoughnutChart from '@/js/components/charts/DoughnutChart.vue';
import moment from 'moment';
import { formatUAH } from '@/js/helpers';

export default {
    name: 'PreviewDamageNote',

    components: {
        DoughnutChart,
    },

    props: ['id'],

    data() {
        return {
            isLoading: false,
            objectTypesLoading: false,
            communitiesLoading: false,
            tab: 'tab1',
            damageNote: null,
            objectTypeItems: [],
            communityItems: [],
            damageTypeItems: [
                {
                    id: 'high',
                    name: 'Повне руйнування',
                },
                {
                    id: 'medium',
                    name: 'Сильне руйнування',
                },
                {
                    id: 'low',
                    name: 'Слабке руйнування',
                }
            ],

            chartOptions: {
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                    animateRotate: false,
                },
                tooltips: {
                    enabled: false
                },
                legend: {
                    display: false
                },
                hover: {
                    mode: null
                },
                cutoutPercentage: '80',
            },
        }
    },

    computed: {
        damageTypeLabel() {
            if (!this.damageNote) {
                return null;
            }

            let damageType = this.damageTypeItems.find((item) => item.id === this.damageNote.damageType);

            return damageType && damageType.name;
        },

        objectCategoryLabel() {
            if (!this.damageNote || !this.objectTypeItems || !this.objectTypeItems.length) {
                return null;
            }

            let objectCategory = this.objectTypeItems.find((item) => item.object_category_id === this.damageNote.objectCategory);

            return objectCategory && objectCategory.object_category && objectCategory.object_category.name;
        },

        objectTypeLabel() {
            if (!this.damageNote || !this.objectTypeItems || !this.objectTypeItems.length) {
                return null;
            }

            let objectType = this.objectTypeItems.find((item) => item.id === this.damageNote.objectType);

            return objectType && objectType.name;
        },

        mockedRestorationFunds() {
            if (!this.damageNote) {
                return 0;
            }

            return Math.floor(Math.random() * this.damageNote.restorationСost) + 1;
        },

        chartData() {
            if (!this.damageNote) {
                return {
                    labels: [],
                    datasets: []
                };
            }

            let progress = (this.mockedRestorationFunds / this.damageNote.restorationСost) * 100;
            progress = Math.round(progress * 100) / 100; // round to two decimal places
            const remaining = 100 - progress;

            return {
                labels: ["Progress", "Remaining"],
                datasets: [
                    {
                        data: [progress, remaining], // Progress and remaining values
                        backgroundColor: ["#ffd500", "#e0e0e0"], // Colors for progress and remaining parts
                        borderWidth: 0, // Remove border for cleaner look
                    },
                ],
            };
        },
    },

    mounted() {
        this.loadDamageNote();
        this.loadObjectTypes();
        // this.loadCommunities();
    },

    methods: {
        formatCurrency(amount) {
            return formatUAH(amount);
        },

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

        loadCommunities() {
            this.communitiesLoading = true;
            this.$store.dispatch('loadCommunities')
                .then((response) => {
                    this.communityItems = response.data || [];
                })
                .catch(() => {
                    //
                })
                .finally(() => {
                    this.communitiesLoading = false;
                });
        },

        loadDamageNote() {
            this.isLoading = true;
            this.$store.dispatch('loadDamageNote', this.id)
                .then((response) => {
                    this.damageNote = this.formatDamageNote(response.data);
                })
                .catch(() => {
                    //
                })
                .finally(() => {
                    this.isLoading = false;
                });
        },

        formatDamageNote(data) {
            if (!data) {
                return null;
            }

            return {
                fullName: data.damage_note_request.full_name,
                email: data.damage_note_request.email,
                phone: data.damage_note_request.phone,
                date: data.date && moment(data.date).format('YYYY-MM-DD'),
                objectCategory: data.object_type && data.object_type.object_category_id,
                objectType: data.object_type_id,
                community: data.community_id,
                city: data.city,
                street: data.street,
                buildingNumber: data.building_number,
                damageType: data.damage_type,
                restorationСost: data.restoration_cost,
                comment: data.comment,
            }
        },
    }
}
</script>

<style lang="scss" scoped>
    .damage-note-tabs__wrapper {
        border-right: 1px solid rgba(0, 0, 0, .12);
    }

    .damage-note-tabs__tab {
        min-width: 100%;
        min-height: 60px;
        justify-content: flex-start;
    }

    .damage-note-doughnut__wrapper {
        position: relative;
    }

    .damage-note-doughnut {
        width: 200px;
        height: 200px;
        margin: 0 auto;
    }

    .damage-note-doughnut__percent {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 24px;
    }

    .damage-note-funds__title {
        max-width: 220px;
        white-space: initial;
        line-height: 1.5;
    }

    .damage-note-funds__value {
        font-size: 40px;
        font-weight: 700;
    }
</style>

