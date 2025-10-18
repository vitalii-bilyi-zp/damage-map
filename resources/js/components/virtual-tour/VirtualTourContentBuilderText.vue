<template>
    <v-expansion-panels v-model="panel" class="builder-item">
        <v-expansion-panel>
            <v-expansion-panel-header>
                <div class="d-flex align-center">
                    <img
                        class="builder-item__header-image mr-3"
                        src="/storage/images/text.png"
                    />
                    <b>Текст</b>
                </div>
            </v-expansion-panel-header>
                <v-expansion-panel-content>
                    <quill-editor
                        v-model="content"
                        ref="myQuillEditor"
                        :options="editorOption"
                        class="builder-item__text-editor"
                    >
                    </quill-editor>
                </v-expansion-panel-content>
        </v-expansion-panel>
    </v-expansion-panels>
</template>

<script>
import { quillEditor } from 'vue-quill-editor';

export default {
    name: 'VirtualTourContentBuilderText',

    components: {
        quillEditor
    },

    props: {

    },

    data() {
        return {
            panel: [],
            content: '',
            editorOption: {
                theme: 'snow', // Simple theme
                placeholder: 'Start typing...',
                modules: {
                    toolbar: [
                        [{'header': [1, 2, 3, false]}], // Heading options (H1, H2, H3, normal text)
                        ['bold', 'italic', 'underline'], // Basic formatting
                        [{ list: 'ordered' }, { list: 'bullet' }], // Lists
                        ['clean'] // Remove formatting
                    ],
                }
            }
        }
    },
}
</script>

<style lang="scss">

</style>
