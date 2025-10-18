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
                        <v-list-item-subtitle class="mb-1">Тип пошкодження</v-list-item-subtitle>
                        <v-list-item-title>{{ damageTypeLabel }}</v-list-item-title>
                    </v-list-item-content>
                </v-list-item>
            </v-col>
            <v-col cols="12" sm="4">
                <v-list-item two-line>
                    <v-list-item-content>
                        <v-list-item-subtitle class="mb-1">Оціночна вартість відновлення</v-list-item-subtitle>
                        <v-list-item-title>{{ formatCurrency(damageNote.restorationCost) }}</v-list-item-title>
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

</style>

