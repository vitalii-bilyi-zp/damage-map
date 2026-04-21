<template>
    <div>
        <v-snackbar
            v-model="snackbarError"
            color="error"
            top
        >
            <v-icon dark class="mr-2">mdi-alert-circle</v-icon>
            Сталася помилка. Будь ласка, спробуйте пізніше.
            <v-btn
                text
                icon
                dark
                @click="snackbarError = false">
                <v-icon size="20">mdi-close</v-icon>
            </v-btn>
        </v-snackbar>

        <v-form>
            <v-select
                v-model="form.objectCategory"
                :items="objectCategoryItemsComputed"
                :error-messages="objectCategoryErrors"
                label="Категорія об’єкта"
                dense
                required
                outlined
                item-text="name"
                item-value="id"
                :disabled="!objectCategoryItemsComputed || !objectCategoryItemsComputed.length"
                @change="onObjectCategoryChange"
                @blur="$v.form.objectCategory.$touch()"
            ></v-select>

            <v-select
                v-model="form.objectType"
                :items="objectTypeItemsComputed"
                :error-messages="objectTypeErrors"
                label="Тип об’єкта"
                dense
                required
                outlined
                item-text="name"
                item-value="id"
                :disabled="!form.objectCategory || !objectTypeItemsComputed || !objectTypeItemsComputed.length"
                @change="$v.form.objectType.$touch()"
                @blur="$v.form.objectType.$touch()"
            ></v-select>

            <v-autocomplete
                v-model="form.community"
                :items="communityItems"
                :error-messages="communityErrors"
                label="Територіальна громада"
                dense
                required
                outlined
                item-text="name"
                item-value="id"
                :disabled="!communityItems || !communityItems.length"
                @change="$v.form.community.$touch()"
                @blur="$v.form.community.$touch()"
            ></v-autocomplete>

            <!-- <v-text-field
                v-model="form.city"
                label="Місто / селище"
                dense
                outlined
                disabled
            ></v-text-field>

            <v-text-field
                v-model="form.street"
                label="Вулиця"
                dense
                outlined
                disabled
            ></v-text-field>

            <v-text-field
                v-model="form.buildingNumber"
                label="Будівля"
                dense
                outlined
                disabled
            ></v-text-field> -->

            <v-text-field
                v-model="form.floors"
                :error-messages="floorsErrors"
                label="Кількість поверхів"
                type="number"
                dense
                required
                outlined
                @input="$v.form.floors.$touch()"
                @blur="$v.form.floors.$touch()"
            />

            <v-text-field
                v-model="form.area"
                :error-messages="areaErrors"
                label="Площа (м²)"
                type="number"
                dense
                suffix="м²"
                required
                outlined
                @input="$v.form.area.$touch()"
                @blur="$v.form.area.$touch()"
            />

            <v-select
                v-model="form.damageType"
                :items="damageTypeItems"
                :error-messages="damageTypeErrors"
                label="Тип пошкодження"
                dense
                required
                outlined
                item-text="name"
                item-value="id"
                @change="$v.form.damageType.$touch()"
                @blur="$v.form.damageType.$touch()"
            ></v-select>

            <v-select
                v-model="form.repairType"
                :items="repairTypeItemsFiltered"
                :error-messages="repairTypeErrors"
                label="Тип ремонту"
                dense
                required
                outlined
                item-text="name"
                item-value="id"
                :disabled="!repairTypeItemsFiltered || !repairTypeItemsFiltered.length"
                @change="$v.form.repairType.$touch()"
                @blur="$v.form.repairType.$touch()"
            />
        </v-form>

        <v-divider/>

        <div class="px-2">
            <v-row>
                <v-col cols="12" sm="6">
                    <v-list-item two-line class="mb-4">
                        <v-list-item-content class="damage-note-funds">
                            <v-list-item-subtitle class="damage-note-funds__title mb-3">Оціночна вартість відновлення</v-list-item-subtitle>
                            <v-list-item-title class="damage-note-funds__value">
                                {{ damageNote.restorationCost === null ? '???' : formatCurrency(damageNote.restorationCost) }}
                            </v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                </v-col>
                <v-col cols="12" sm="6">
                    <v-spacer></v-spacer>
                    <div>
                        <v-list-item two-line class="mb-4 pl-0">
                            <v-list-item-content class="damage-note-funds">
                                <v-list-item-subtitle class="damage-note-funds__title mb-3">Прогнозована вартість відновлення</v-list-item-subtitle>
                                <v-list-item-title class="damage-note-funds__value" :class="{ 'warning--text': adjustedCost !== null }">
                                    {{ displayedPredictedCost === null ? '???' : formatCurrency(displayedPredictedCost) }}
                                </v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>

                        <v-btn
                            color="success"
                            :loading="formLoading"
                            :disabled="formLoading"
                            @click="submit"
                        >
                            Розрахувати
                            <template v-slot:loader>
                                <span class="custom-loader">
                                    <v-icon light>mdi-cached</v-icon>
                                </span>
                            </template>
                        </v-btn>
                    </div>
                </v-col>
            </v-row>
            <!-- Tabs: visible after first calculation -->
            <template v-if="predictionMade">
                <v-divider class="my-4" />

                <v-tabs v-model="activeTab" dense>
                    <v-tab>SHAP-внески</v-tab>
                    <v-tab>Інфляційне коригування</v-tab>
                </v-tabs>

                <v-tabs-items v-model="activeTab">
                    <!-- Tab 1: Pie chart -->
                    <v-tab-item>
                        <v-row v-if="chartData && chartData.datasets && chartData.datasets.length" class="justify-center mt-2">
                            <v-col cols="12" sm="6">
                                <PieChart :chart-data="chartData" :options="chartOptions" class="statistic-card__chart" />
                            </v-col>
                        </v-row>
                        <div v-else class="text-center grey--text py-4 caption">
                            Дані недоступні
                        </div>
                    </v-tab-item>

                    <!-- Tab 2: Inflation correction -->
                    <v-tab-item>
                        <div class="pt-4">
                            <v-row>
                                <v-col cols="12" sm="6">
                                    <v-select
                                        v-model="work_year"
                                        :items="availableYears"
                                        label="Рік початку робіт"
                                        clearable
                                        outlined
                                        dense
                                    />
                                </v-col>

                                <v-col cols="12" sm="6">
                                    <v-select
                                        v-model="work_month"
                                        :items="availableMonths"
                                        item-text="label"
                                        item-value="value"
                                        label="Місяць початку робіт"
                                        clearable
                                        outlined
                                        dense
                                        :disabled="!work_year"
                                        @change="onMonthSelected"
                                    />
                                </v-col>
                            </v-row>

                            <template v-if="adjustedCost">
                                <v-row class="mb-2">
                                    <v-col cols="12">
                                        <div class="caption grey--text">Прогнозована вартість (скоригована)</div>
                                        <div class="text-h6 warning--text font-weight-bold">
                                            {{ formatCurrency(adjustedCost) }} грн
                                            <v-progress-circular
                                                v-if="inflationLoading"
                                                indeterminate
                                                size="16"
                                                width="2"
                                                class="ml-2"
                                            />
                                        </div>
                                        <div class="caption grey--text">{{ inflationHint }}</div>
                                    </v-col>
                                </v-row>

                                <bar-chart
                                    v-if="scenarioChartData"
                                    :chart-data="scenarioChartData"
                                    :options="scenarioChartOptions"
                                    :height="160"
                                />
                            </template>

                            <v-row v-else-if="inflationLoading" justify="center" class="my-4">
                                <v-progress-circular indeterminate color="primary" />
                            </v-row>
                        </div>
                    </v-tab-item>
                </v-tabs-items>
            </template>
        </div>
    </div>
</template>

<script>
import PieChart from '@/js/components/charts/PieChart.vue';
import BarChart from '@/js/components/charts/BarChart.vue';
import { required, minValue } from 'vuelidate/lib/validators';
import { formatUAH } from '@/js/helpers';

const HERITAGE_ALLOWED_REPAIR_CODES = {
    none:     null,
    local:    null,
    regional: ['current_repair', 'capital_repair', 'restoration', 'conservation'],
    national: ['restoration', 'conservation'],
    world:    ['restoration'],
};

const COLORS = [
    '#0aadd0',
    '#e43e6e',
    '#01c498',
    '#ffbe07',
    '#4e40de',
    '#7f2084',
    '#795548',
    '#9e9e9e',
    '#ff5722',
    '#3f51b5',
    '#8bc34a',
    '#ff9800',
    '#009688',
    '#607d8b'
];

export default {
    name: 'DamageNoteRestorationCost',

    components: {
        PieChart,
        BarChart,
    },

    props: {
        damageNote: {
            type: Object,
            required: true
        }
    },

    data() {
        return {
            objectTypesLoading: false,
            communitiesLoading: false,
            repairTypesLoading: false,
            objectTypeItems: [],
            communityItems: [],
            repairTypeItems: [],
            formLoading: false,
            snackbarError: false,
            predictedRestorationCost: null,
            form: {
                objectCategory: null,
                objectType: null,
                community: null,
                city: null,
                street: null,
                buildingNumber: null,
                floors: null,
                area: null,
                damageType: null,
                repairType: null,
            },

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

            chartData: {
                labels: [],
                datasets: []
            },
            chartOptions: {
                responsive: true,
                maintainAspectRatio: false
            },

            activeTab: 0,

            // Inflation correction state
            predictionMade: false,
            basePredictedCost: null,
            work_year: null,
            work_month: null,
            adjustedCost: null,
            inflationK: null,
            inflationLoading: false,
            scenarioChartData: null,

            YEARS: [2024, 2025, 2026, 2027],
            MONTHS: [
                { value: 1,  label: 'Січень' },
                { value: 2,  label: 'Лютий' },
                { value: 3,  label: 'Березень' },
                { value: 4,  label: 'Квітень' },
                { value: 5,  label: 'Травень' },
                { value: 6,  label: 'Червень' },
                { value: 7,  label: 'Липень' },
                { value: 8,  label: 'Серпень' },
                { value: 9,  label: 'Вересень' },
                { value: 10, label: 'Жовтень' },
                { value: 11, label: 'Листопад' },
                { value: 12, label: 'Грудень' },
            ],
            scenarioChartOptions: {
                responsive: true,
                maintainAspectRatio: false,
                legend: { display: false },
                tooltips: {
                    callbacks: {
                        label: (item) => {
                            return new Intl.NumberFormat('uk-UA').format(item.yLabel) + ' грн';
                        }
                    }
                },
                scales: {
                    yAxes: [{
                        ticks: {
                            callback: (value) => (value / 1_000_000).toFixed(0) + ' млн',
                            fontSize: 10,
                        }
                    }],
                    xAxes: [{
                        ticks: { fontSize: 10, maxRotation: 45 }
                    }]
                }
            },
        }
    },

    validations() {
        return {
            form: {
                objectCategory: { required },
                objectType: { required },
                community: { required },
                floors: { required, minValue: minValue(1) },
                area: { required, minValue: minValue(1) },
                damageType: { required },
                repairType: { required },
            }
        };
    },

    computed: {
        objectCategoryErrors() {
            const errors = [];
            if (!this.$v.form.objectCategory.$dirty) return errors;
            !this.$v.form.objectCategory.required && errors.push('Це поле обов\'язкове')
            return errors;
        },

        objectTypeErrors() {
            const errors = [];
            if (!this.$v.form.objectType.$dirty) return errors;
            !this.$v.form.objectType.required && errors.push('Це поле обов\'язкове')
            return errors;
        },

        communityErrors() {
            const errors = [];
            if (!this.$v.form.community.$dirty) return errors;
            !this.$v.form.community.required && errors.push('Це поле обов\'язкове')
            return errors;
        },

        floorsErrors() {
            const errors = [];
            if (!this.$v.form.floors.$dirty) return errors;
            !this.$v.form.floors.required && errors.push('Це поле обов\'язкове');
            this.$v.form.floors.required && !this.$v.form.floors.minValue && errors.push('Мінімум 1');
            return errors;
        },

        areaErrors() {
            const errors = [];
            if (!this.$v.form.area.$dirty) return errors;
            !this.$v.form.area.required && errors.push('Це поле обов\'язкове');
            this.$v.form.area.required && !this.$v.form.area.minValue && errors.push('Мінімум 1 м²');
            return errors;
        },

        damageTypeErrors() {
            const errors = [];
            if (!this.$v.form.damageType.$dirty) return errors;
            !this.$v.form.damageType.required && errors.push('Це поле обов\'язкове')
            return errors;
        },

        repairTypeErrors() {
            const errors = [];
            if (!this.$v.form.repairType.$dirty) return errors;
            !this.$v.form.repairType.required && errors.push('Це поле обов\'язкове');
            return errors;
        },

        objectCategoryItemsComputed() {
            const objectCategories = this.objectTypeItems.reduce((prev, curr) => {
                if (curr.object_category && !prev[curr.object_category.id]) {
                    prev[curr.object_category.id] = curr.object_category;
                }

                return prev;
            }, {});

            return Object.values(objectCategories);
        },

        objectTypeItemsComputed() {
            if (!this.form.objectCategory) {
                return [];
            }

            return this.objectTypeItems.filter((item) => item.object_category_id === this.form.objectCategory);
        },

        heritageStatus() {
            return (this.damageNote && this.damageNote.heritageStatus) || 'none';
        },

        repairTypeItemsFiltered() {
            const allowed = HERITAGE_ALLOWED_REPAIR_CODES[this.heritageStatus];
            if (!allowed) return this.repairTypeItems;
            return this.repairTypeItems.filter((t) => allowed.includes(t.code));
        },

        currentYear() {
            return new Date().getFullYear();
        },
        currentMonth() {
            return new Date().getMonth() + 1;
        },

        availableYears() {
            return this.YEARS.filter(year => year >= this.currentYear);
        },

        availableMonths() {
            if (!this.work_year) return this.MONTHS;
            if (this.work_year === this.currentYear) {
                return this.MONTHS.filter(m => m.value >= this.currentMonth);
            }
            return this.MONTHS;
        },

        displayedPredictedCost() {
            return this.adjustedCost ?? this.basePredictedCost;
        },

        inflationHint() {
            if (!this.adjustedCost || !this.inflationK || !this.basePredictedCost) return '';
            const delta = ((this.inflationK - 1) * 100).toFixed(1);
            const base  = this.formatCurrency(this.basePredictedCost);
            const month = this.MONTHS.find(x => x.value === this.work_month)?.label || '';
            return `Базовий: ${base} · ×${this.inflationK.toFixed(4)} (+${delta}%) · ${month} ${this.work_year}`;
        },
    },

    watch: {
        damageNote() {
            this.initForm();
        },

        heritageStatus(val) {
            const allowed = HERITAGE_ALLOWED_REPAIR_CODES[val];
            if (allowed && this.form.repairType) {
                const current = this.repairTypeItems.find((t) => t.id === this.form.repairType);
                if (current && !allowed.includes(current.code)) {
                    this.form.repairType = null;
                    this.$v.form.repairType.$reset();
                }
            }
        },

        work_year() {
            if (this.work_month) {
                const stillAvailable = this.availableMonths.some(
                    m => m.value === this.work_month
                );
                if (!stillAvailable) {
                    this.work_month = null;
                }
            }
            this.adjustedCost     = null;
            this.inflationK       = null;
            this.scenarioChartData = null;
        },
    },

    mounted() {
        this.loadObjectTypes();
        this.loadCommunities();
        this.loadRepairTypes();
        this.initForm();
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

        loadRepairTypes() {
            this.repairTypesLoading = true;
            this.$store.dispatch('loadRepairTypes')
                .then((response) => {
                    this.repairTypeItems = response.data || [];
                })
                .catch(() => {
                    //
                })
                .finally(() => {
                    this.repairTypesLoading = false;
                });
        },

        initForm() {
            this.form.objectCategory = this.damageNote ? this.damageNote.objectCategory?.id : null;
            this.form.objectType = this.damageNote ? this.damageNote.objectType?.id : null;
            this.form.community = this.damageNote ? this.damageNote.community?.id : null;
            this.form.city = this.damageNote ? this.damageNote.city : null;
            this.form.street = this.damageNote ? this.damageNote.street : null;
            this.form.buildingNumber = this.damageNote ? this.damageNote.buildingNumber : null;
            this.form.floors = this.damageNote ? parseFloat(this.damageNote.floors) : null;
            this.form.area = this.damageNote ? parseFloat(this.damageNote.area) : null;
            this.form.damageType = this.damageNote ? this.damageNote.damageType : null;
            this.form.repairType = this.damageNote ? this.damageNote.repairType?.id : null;
            this.predictedRestorationCost = this.damageNote ? this.damageNote.predictedRestorationCost : null;
        },

        formatCurrency(amount) {
            return formatUAH(amount);
        },

        onObjectCategoryChange() {
            this.objectType = null;
            this.$v.form.objectType.$reset();
            this.$v.form.objectCategory.$touch();
        },

        submit() {
            this.$v.$touch();

            if (this.$v.$invalid) {
                return;
            }

            let data = this.prepareFormData();

            this.formLoading = true;
            this.$store.dispatch('predictRestorationCost', { data })
                .then((response) => {
                    this.predictedRestorationCost = response.data.predicted_cost;

                    if (response.data.pie) {
                        this.setChartData(response.data.pie);
                    }

                    this.predictionMade = true;
                    this.basePredictedCost = response.data.predicted_cost;
                    this.activeTab = 0;
                    this.work_year = null;
                    this.work_month = null;
                    this.adjustedCost = null;
                    this.inflationK = null;
                    this.scenarioChartData = null;
                })
                .catch(() => {
                    this.snackbarError = true;
                })
                .finally(() => {
                    this.formLoading = false;
                });
        },

        prepareFormData() {
            return {
                object_type_id: this.form.objectType,
                community_id: this.form.community,
                floors: this.form.floors,
                area: this.form.area,
                damage_type: this.form.damageType,
                repair_type_id: this.form.repairType,
            };
        },

        async onMonthSelected() {
            if (!this.work_year || !this.work_month) return;

            this.inflationLoading = true;
            this.scenarioChartData = null;

            try {
                const now          = new Date();
                const currentYear  = now.getFullYear();
                const currentMonth = now.getMonth() + 1;

                const periods = [];
                [2024, 2025, 2026, 2027].forEach(year => {
                    [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12].forEach(month => {
                        if (year < currentYear) return;
                        if (year === currentYear && month < currentMonth) return;
                        periods.push({ year, month });
                    });
                });

                const payload = {
                    object_type_id: this.form.objectType,
                    community_id:   this.form.community,
                    repair_type_id: this.form.repairType,
                    damage_type:    this.form.damageType,
                    area:           this.form.area,
                    floors:         this.form.floors,
                    periods,
                };

                const response = await window.httpClient.post(
                    '/api/predict-restoration-cost/scenario-comparison',
                    payload
                );

                const scenarios = response.data.scenarios;

                const selected = scenarios.find(
                    s => s.year === this.work_year && s.month === this.work_month
                );
                if (selected) {
                    this.adjustedCost = selected.adjusted_cost;
                    this.inflationK   = selected.inflation_k;
                }

                this.scenarioChartData = this.buildChartData(scenarios);

            } catch (error) {
                console.error('Inflation scenario error:', error);
            } finally {
                this.inflationLoading = false;
            }
        },

        buildChartData(scenarios) {
            const monthName = (m) => this.MONTHS.find(x => x.value === m)?.label || m;
            const labels = scenarios.map(s => `${monthName(s.month)} ${s.year}`);
            const values = scenarios.map(s => Math.round(s.adjusted_cost));
            const colors = scenarios.map(s =>
                s.year === this.work_year && s.month === this.work_month
                    ? '#F57F17'
                    : 'rgba(25, 118, 210, 0.4)'
            );

            return {
                labels,
                datasets: [{
                    data:            values,
                    backgroundColor: colors,
                    borderRadius:    3,
                }]
            };
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
}
</script>

<style lang="scss" scoped>
    .damage-note-funds__title {
        font-size: 1rem;
        white-space: initial;
        line-height: 1.5;
    }

    .damage-note-funds__value {
        font-size: 40px;
        font-weight: 700;
    }

    .custom-loader {
        animation: loader 1s infinite;
        display: flex;
    }

    @-moz-keyframes loader {
        from {
            transform: rotate(0);
        }
        to {
            transform: rotate(360deg);
        }
    }
    @-webkit-keyframes loader {
        from {
            transform: rotate(0);
        }
        to {
            transform: rotate(360deg);
        }
    }
    @-o-keyframes loader {
        from {
            transform: rotate(0);
        }
        to {
            transform: rotate(360deg);
        }
    }
    @keyframes loader {
        from {
            transform: rotate(0);
        }
        to {
            transform: rotate(360deg);
        }
    }
</style>

