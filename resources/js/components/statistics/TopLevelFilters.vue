<template>
    <v-card class="statistic-card">
        <v-toolbar flat color="white" height="64">
            <v-row>
                <v-col cols="12" sm="6">
                    <v-menu
                        v-model="periodMenu"
                        :close-on-content-click="false"
                        transition="scale-transition"
                        offset-y
                        min-width="280px"
                        :nudge-bottom="10"
                    >
                        <template v-slot:activator="{ on }">
                            <v-text-field
                                v-on="on"
                                v-model="periodText"
                                dense
                                outlined
                                readonly
                                hide-details
                                placeholder="Період"
                                append-icon="mdi-calendar"
                            ></v-text-field>
                        </template>
                        <v-date-picker
                            v-model="datePeriod"
                            range
                            scrollable
                            min="2022-02-24"
                            :max="new Date().toISOString().substr(0, 10)"
                        >
                            <v-spacer></v-spacer>
                            <v-btn text color="primary" @click="periodMenu = false">Cancel</v-btn>
                            <v-btn text color="primary" @click="savePeriod">OK</v-btn>
                        </v-date-picker>
                    </v-menu>
                </v-col>

                <v-col cols="12" sm="6">
                    <v-autocomplete
                        v-model="regionComputed"
                        :items="regionItems"
                        clearable
                        dense
                        outlined
                        hide-details
                        item-text="name"
                        item-value="id"
                        placeholder="Регіон"
                        :disabled="!regionItems || !regionItems.length"
                    ></v-autocomplete>
                </v-col>
            </v-row>
        </v-toolbar>
    </v-card>
</template>

<script>
import moment from 'moment';

export default {
    name: 'TopLevelFilters',

    data() {
        return {
            periodMenu: false,
            datePeriod: [],
            isPeriodSaved: false,
            regionsLoading: false,
            regionItems: [],
        };
    },

    computed: {
        filters() {
            return this.$store.state.filters;
        },
        periodText() {
            return this.filters.period.join(' ~ ');
        },
        regionComputed: {
            get: function() {
                return this.filters.region;
            },
            set: function(newValue) {
                this.$store.dispatch('setFilters', {...this.filters, region: newValue})
            }
        }
    },

    watch: {
        periodMenu(val) {
            if (!val) {
                if (!this.isPeriodSaved) {
                    this.datePeriod = this.filters.period;
                } else {
                    this.isPeriodSaved = false;
                }
            }
        },
    },

    mounted() {
        this.datePeriod = this.filters.period.slice();
        this.loadRegions();
    },

    methods: {
        loadRegions() {
            this.regionsLoading = true;
            this.$store.dispatch('loadRegions')
                .then((response) => {
                    this.regionItems = response.data || [];
                })
                .catch(() => {
                    //
                })
                .finally(() => {
                    this.regionsLoading = false;
                });
        },

        savePeriod() {
            if (this.datePeriod.length < 2) {
                this.periodMenu = false;
                return;
            }

            if (moment(this.datePeriod[0]).isAfter(this.datePeriod[1])) {
                this.$store.dispatch('setFilters', {...this.filters, period: this.datePeriod.reverse()})
            } else {
                this.$store.dispatch('setFilters', {...this.filters, period: this.datePeriod})
            }

            this.isPeriodSaved = true;
            this.periodMenu = false;
        }
    }
};
</script>