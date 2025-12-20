<template>
    <div>
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
                        <v-list-item-subtitle class="mb-1">Категорія об’єкта</v-list-item-subtitle>
                        <v-list-item-title>{{ damageNote.objectCategory ? damageNote.objectCategory.name : '-' }}</v-list-item-title>
                    </v-list-item-content>
                </v-list-item>
            </v-col>
            <v-col cols="12" sm="4">
                <v-list-item two-line>
                    <v-list-item-content>
                        <v-list-item-subtitle class="mb-1">Тип об’єкта</v-list-item-subtitle>
                        <v-list-item-title>{{ damageNote.objectType ? damageNote.objectType.name : '-' }}</v-list-item-title>
                    </v-list-item-content>
                </v-list-item>
            </v-col>
        </v-row>

        <v-divider/>

        <v-row>
            <v-col cols="12" sm="4">
                <v-list-item two-line>
                    <v-list-item-content>
                        <v-list-item-subtitle class="mb-1">Регіон</v-list-item-subtitle>
                        <v-list-item-title>{{ damageNote.community && damageNote.community.district && damageNote.community.district.region ? damageNote.community.district.region.name : '-' }}</v-list-item-title>
                    </v-list-item-content>
                </v-list-item>
            </v-col>
            <v-col cols="12" sm="4">
                <v-list-item two-line>
                    <v-list-item-content>
                        <v-list-item-subtitle class="mb-1">Район</v-list-item-subtitle>
                        <v-list-item-title>{{ damageNote.community && damageNote.community.district ? damageNote.community.district.name : '-' }}</v-list-item-title>
                    </v-list-item-content>
                </v-list-item>
            </v-col>
            <v-col cols="12" sm="4">
                <v-list-item two-line>
                    <v-list-item-content>
                        <v-list-item-subtitle class="mb-1">Територіальна громада</v-list-item-subtitle>
                        <v-list-item-title>{{ damageNote.community ? damageNote.community.name : '-' }}</v-list-item-title>
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
                        <v-list-item-subtitle class="mb-1">Кількість поверхів</v-list-item-subtitle>
                        <v-list-item-title>{{ damageNote.floors || '-' }}</v-list-item-title>
                    </v-list-item-content>
                </v-list-item>
            </v-col>
            <v-col cols="12" sm="4">
                <v-list-item two-line>
                    <v-list-item-content>
                        <v-list-item-subtitle class="mb-1">Площа (м²)</v-list-item-subtitle>
                        <v-list-item-title>{{ damageNote.area ? damageNote.area + ' м²' : '-' }}</v-list-item-title>
                    </v-list-item-content>
                </v-list-item>
            </v-col>
        </v-row>

        <v-divider/>

        <v-row>
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
                        <v-list-item-subtitle class="mb-1">Тип ремонту</v-list-item-subtitle>
                        <v-list-item-title>{{ damageNote.repairType ? damageNote.repairType.name : '-' }}</v-list-item-title>
                    </v-list-item-content>
                </v-list-item>
            </v-col>
        </v-row>

        <v-divider/>

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
                            <v-list-item-title class="damage-note-funds__value">
                                {{ damageNote.predictedRestorationCost === null ? '???' : formatCurrency(damageNote.predictedRestorationCost) }}
                            </v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                </div>
            </v-col>
        </v-row>
    </div>
</template>

<script>
import { formatUAH } from '@/js/helpers';

export default {
    name: 'DamageNoteGeneralInfo',

    props: {
        damageNote: {
            type: Object,
            required: true
        }
    },

    data() {
        return {
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
    },

    methods: {
        formatCurrency(amount) {
            return formatUAH(amount);
        },
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
</style>

