<template>
    <div class="damage-form">
        <v-form>
            <v-text-field
                v-model="fileName"
                label="Назва"
                dense
                outlined
            ></v-text-field>

            <v-file-input
                v-model="file"
                :error-messages="fileErrors"
                placeholder="Оберіть файл"
                dense
                filled
                prepend-icon="mdi-file"
                accept=".txt, .doc, .docx, .pdf"
                @change="$v.file.$touch()"
                @blur="$v.file.$touch()"
            ></v-file-input>
        </v-form>

        <v-divider/>

        <div class="damage-form__actions">
            <v-spacer></v-spacer>
            <v-btn
                color="success"
                :loading="formLoading"
                :disabled="formLoading"
                @click="submit"
            >
                Надіслати
                <template v-slot:loader>
                    <span class="custom-loader">
                        <v-icon light>mdi-cached</v-icon>
                    </span>
                </template>
            </v-btn>
        </div>
    </div>
</template>

<script>
import { required } from 'vuelidate/lib/validators'

export default {
    name: 'RegulationDocumentForm',

    props: {

    },

    data() {
        return {
            formLoading: false,
            fileName: '',
            file: null,
        }
    },

    validations: {
        file: { required },
    },

    computed: {
        fileErrors() {
            const errors = [];
            if (!this.$v.file.$dirty) return errors;
            !this.$v.file.required && errors.push('Це поле обов\'язкове')
            return errors;
        },
    },

    methods: {
        submit() {
            this.$v.file.$touch();

            if (this.$v.file.$invalid) {
                return;
            }

            let data = this.prepareFormData();
            this.$emit('submit-form', data);
        },

        prepareFormData() {
            let data = new FormData();

            data.append('file_name', this.fileName);
            data.append('file', this.file);

            return data;
        },

        clearForm() {
            this.fileName = '';
            this.$v.file.$reset();
            this.file = null;
        },
    }
}
</script>

<style lang="scss" scoped>
.damage-form__actions {
    display: flex;
    padding-top: 16px;
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

