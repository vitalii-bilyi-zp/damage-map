<template>
    <v-dialog
        v-model="dialog"
        :max-width="options.width"
        :style="{ zIndex: options.zIndex }"
        @keydown.esc="cancel"
    >
        <v-card>
            <v-card-title class="text-h5 grey lighten-2 px-4">
                Підтвердження операції
            </v-card-title>

            <template>
                <v-card-text class="pa-4">
                    <v-expansion-panels flat>
                        <v-expansion-panel>
                            <v-expansion-panel-header>
                                Ви впевнені, що хочете відхилити цей запит?
                            </v-expansion-panel-header>
                            <v-expansion-panel-content>
                                <v-simple-table>
                                    <template v-slot:default>
                                        <tbody>
                                            <tr>
                                                <td>Дата</td>
                                                <td>{{ formatDate(item.date) }}</td>
                                            </tr>
                                            <tr>
                                                <td>Тип об’єкта</td>
                                                <td>{{ item.object_type }}</td>
                                            </tr>
                                            <tr>
                                                <td>Територіальна громада</td>
                                                <td>{{ item.community }}</td>
                                            </tr>
                                            <tr>
                                                <td>Місто / селище</td>
                                                <td>{{ item.city }}</td>
                                            </tr>
                                            <tr>
                                                <td>Вулиця</td>
                                                <td>{{ item.street }}</td>
                                            </tr>
                                            <tr>
                                                <td>Будівля</td>
                                                <td>{{ item.building_number }}</td>
                                            </tr>
                                            <tr>
                                                <td>Тип пошкодження</td>
                                                <td>{{ formatDamageType(item.damage_type) }}</td>
                                            </tr>
                                            <tr>
                                                <td>Вартість відновлення</td>
                                                <td>{{ item.restoration_cost }}</td>
                                            </tr>
                                        </tbody>
                                    </template>
                                </v-simple-table>
                            </v-expansion-panel-content>
                        </v-expansion-panel>
                    </v-expansion-panels>

                    <v-form class="pt-4 px-4">
                        <v-textarea
                            v-model="comment"
                            :error-messages="commentErrors"
                            label="Коментар"
                            dense
                            outlined
                            rows="3"
                            @change="$v.comment.$touch()"
                            @blur="$v.comment.$touch()"
                        ></v-textarea>
                    </v-form>
                </v-card-text>

                <v-divider></v-divider>
            </template>

            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn
                    v-if="!options.noconfirm"
                    color="grey"
                    text
                    class="body-2 font-weight-bold"
                    @click.native="cancel"
                >
                    Закрити
                </v-btn>
                <v-btn
                    color="primary"
                    class="body-2 font-weight-bold ml-4"
                    outlined
                    @click.native="agree"
                >
                    Відхилити
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
import moment from 'moment';
import { required } from 'vuelidate/lib/validators';

export default {
    name: 'DeclineRequestDialog',

    data() {
        return {
            dialog: false,
            resolve: null,
            reject: null,
            item: null,
            comment: '',
            options: {
                width: 600,
                zIndex: 200,
                noconfirm: false
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
        };
    },

    validations: {
        comment: { required },
    },

    computed: {
        commentErrors() {
            const errors = [];
            if (!this.$v.comment.$dirty) return errors;
            !this.$v.comment.required && errors.push('Це поле обов\'язкове')
            return errors;
        },
    },

    methods: {
        open(item, options) {
            this.dialog = true;
            this.item = item;
            this.clearForm();
            this.options = Object.assign(this.options, options);
            return new Promise((resolve, reject) => {
                this.resolve = resolve;
                this.reject = reject;
            });
        },

        clearForm() {
            this.$v.$reset();
            this.comment = '';
        },

        cancel() {
            if (this.options.noconfirm) {
                return;
            }

            this.resolve({
                isConfirmed: false,
            });
            this.dialog = false;
        },

        agree() {
            this.$v.$touch();

            if (this.$v.$invalid) {
                return;
            }

            this.resolve({
                isConfirmed: true,
                comment: this.comment
            });
            this.dialog = false;
        },

        formatDate(date) {
            return moment(date).format('YYYY-MM-DD');
        },

        formatDamageType(type) {
            const foundItem = this.damageTypeItems.find((item) => item.id === type);
            return foundItem ? foundItem.name || type : type;
        },
    }
};
</script>
