<template>
    <v-dialog
        v-model="dialog"
        :max-width="options.width"
        :style="{ zIndex: options.zIndex }"
        @keydown.esc="cancel"
    >
        <v-card>
            <!-- <v-card-title class="text-h5 grey lighten-2">
                Підтвердження операції
            </v-card-title>

            <v-card-text>
                Ви впевнені, що хочете видалити цей запис?
            </v-card-text> -->

            <v-card-title class="text-h5 grey lighten-2 px-4">
                {{ title }}
            </v-card-title>

            <template v-if="message">
                <v-card-text class="pa-4">
                    {{ message }}
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
                    Відхилити
                </v-btn>
                <v-btn
                    color="primary"
                    class="body-2 font-weight-bold ml-4"
                    outlined
                    @click.native="agree"
                >
                    Підтвердити
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
export default {
    name: 'ConfirmDialog',

    data() {
        return {
            dialog: false,
            resolve: null,
            reject: null,
            title: null,
            message: null,
            options: {
                width: 400,
                zIndex: 200,
                noconfirm: false
            }
        };
    },

    methods: {
        open(title, message, options) {
            this.dialog = true;
            this.title = title;
            this.message = message;
            this.options = Object.assign(this.options, options);
            return new Promise((resolve, reject) => {
                this.resolve = resolve;
                this.reject = reject;
            });
        },

        cancel() {
            if (this.options.noconfirm) {
                return;
            }

            this.resolve(false);
            this.dialog = false;
        },

        agree() {
            this.resolve(true);
            this.dialog = false;
        },
    }
};
</script>
