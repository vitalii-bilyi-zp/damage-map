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
                                            mdi-cash-sync
                                        </v-icon>
                                        Вартість відновлення
                                    </v-tab>
                                    <v-tab key="tab3" class="damage-note-tabs__tab">
                                        <v-icon left>
                                            mdi-cash-multiple
                                        </v-icon>
                                        Фінансування
                                    </v-tab>
                                    <v-tab key="tab4" class="damage-note-tabs__tab">
                                        <v-icon left>
                                            mdi-image
                                        </v-icon>
                                        Галерея
                                    </v-tab>
                                </v-tabs>
                            </v-col>

                            <v-col cols="12" sm="8">
                                <v-tabs-items v-model="tab">
                                    <v-tab-item key="tab1">
                                        <v-card flat>
                                            <v-card-text>
                                                <DamageNoteGeneralInfo :damage-note="damageNote" />
                                            </v-card-text>
                                        </v-card>
                                    </v-tab-item>

                                    <v-tab-item key="tab2">
                                        <v-card flat>
                                            <v-card-text>
                                                <DamageNoteRestorationCost :damage-note="damageNote" />
                                            </v-card-text>
                                        </v-card>
                                    </v-tab-item>

                                    <v-tab-item key="tab3">
                                        <v-card flat>
                                            <v-card-text>
                                                <v-row>
                                                    <v-col cols="12" sm="6">
                                                        <v-list-item two-line class="mb-4">
                                                            <v-list-item-content class="damage-note-funds">
                                                                <v-list-item-subtitle class="damage-note-funds__title mb-3">Оціночна вартість відновлення</v-list-item-subtitle>
                                                                <v-list-item-title class="damage-note-funds__value">{{ formatCurrency(damageNote.restorationCost) }}</v-list-item-title>
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
                                                                <v-list-item-subtitle class="damage-note-funds__title mb-3 mx-auto text-center">Фінансове покриття</v-list-item-subtitle>
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

                                    <v-tab-item key="tab4">
                                        <div class="damage-note-gallery">
                                            <ul v-if="damageNote.images && damageNote.images.length" class="el-upload-list el-upload-list--picture-card">
                                                <li class="el-upload-list__item is-ready" v-for="image in damageNote.images" :key="image.id">
                                                    <img :src="image.file_path" alt="" class="el-upload-list__item-thumbnail">
                                                    <span class="el-upload-list__item-actions">
                                                        <span class="el-upload-list__item-preview" @click="handleImagePreview(image.file_path)">
                                                            <i class="el-icon-zoom-in"></i>
                                                        </span>
                                                    </span>
                                                </li>
                                            </ul>

                                            <div v-else class="damage-note-gallery__empty">
                                                <p class="damage-note-gallery__empty-text">
                                                    ЗОБРАЖЕННЯ ВІДСУТНІ
                                                </p>
                                            </div>

                                            <el-dialog :visible.sync="dialogVisible">
                                                <img width="100%" :src="dialogImageUrl" alt>
                                            </el-dialog>
                                        </div>
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
import DamageNoteGeneralInfo from '@/js/components/damage-note/tabs/DamageNoteGeneralInfo.vue';
import DamageNoteRestorationCost from '@/js/components/damage-note/tabs/DamageNoteRestorationCost.vue';
import DoughnutChart from '@/js/components/charts/DoughnutChart.vue';
import moment from 'moment';
import { formatUAH } from '@/js/helpers';

export default {
    name: 'PreviewDamageNote',

    components: {
        DamageNoteGeneralInfo,
        DamageNoteRestorationCost,
        DoughnutChart,
    },

    props: ['id'],

    data() {
        return {
            isLoading: false,
            tab: 'tab1',
            damageNote: null,
            damageTypeItems: [
                {
                    id: 'low',
                    name: 'Легке',
                },
                {
                    id: 'medium',
                    name: 'Середнє',
                },
                {
                    id: 'high',
                    name: 'Тяжке',
                },
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

            dialogImageUrl: '',
            dialogVisible: false,
        }
    },

    computed: {
        mockedRestorationFunds() {
            if (!this.damageNote) {
                return 0;
            }

            return Math.floor(Math.random() * this.damageNote.restorationCost) + 1;
        },

        chartData() {
            if (!this.damageNote) {
                return {
                    labels: [],
                    datasets: []
                };
            }

            let progress = (this.mockedRestorationFunds / this.damageNote.restorationCost) * 100;
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
    },

    methods: {
        formatCurrency(amount) {
            return formatUAH(amount);
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
                objectCategory: data.object_type && data.object_type.object_category,
                objectType: data.object_type,
                community: data.community,
                district: data.community && data.community.district,
                region: data.community && data.community.district && data.community.district.region,
                city: data.city,
                street: data.street,
                buildingNumber: data.building_number,
                floors: data.floors,
                area: data.area,
                damageType: data.damage_type,
                repairType: data.repair_type,
                heritageStatus: data.heritage_status || 'none',
                restorationCost: data.restoration_cost,
                predictedRestorationCost: data.predicted_restoration_cost,
                comment: data.comment,
                images: data.damage_note_images,
            }
        },

        handleImagePreview(filePath) {
            this.dialogImageUrl = filePath;
            this.dialogVisible = true;
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
        max-width: 250px;
        font-size: 1rem;
        white-space: initial;
        line-height: 1.5;
    }

    .damage-note-funds__value {
        font-size: 40px;
        font-weight: 700;
    }

    .damage-note-gallery {
        position: relative;
        min-height: 178px;
        padding: 15px 15px 0 15px;
        border-radius: 4px;
        background-color: rgba(0, 0, 0, .05);

        .el-upload-list--picture-card {
            padding-left: 0;
        }
    }

    .damage-note-gallery__empty {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 15px;
    }

    .damage-note-gallery__empty-text {
        margin: 0;
    }
</style>

