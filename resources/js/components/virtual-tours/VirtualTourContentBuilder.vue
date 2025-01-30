<template>
    <div class="builder">
        <draggable v-model="builderItems" ghost-class="builder-item--ghost">
            <component v-for="(item, index) in builderItems" :key="`${item.id}-${index}`" :is="item.component" class="mb-5"></component>
        </draggable>

        <v-btn block color="primary" outlined class="builder__add-item" height="48" @click="openDialog">
            <v-icon dark>
                mdi-plus
            </v-icon>
        </v-btn>

        <VirtualTourContentBuilderDialog ref="dialog" @select-item="selectItem" />
    </div>
</template>

<script>
import VirtualTourContentBuilderDialog from '@/js/components/virtual-tours/VirtualTourContentBuilderDialog';
import VirtualTourContentBuilderText from '@/js/components/virtual-tours/VirtualTourContentBuilderText';
import VirtualTourContentBuilderImage from '@/js/components/virtual-tours/VirtualTourContentBuilderImage';
import VirtualTourContentBuilderSlider from '@/js/components/virtual-tours/VirtualTourContentBuilderSlider';
import VirtualTourContentBuilder3d from '@/js/components/virtual-tours/VirtualTourContentBuilder3d';
import draggable from 'vuedraggable';

export default {
    name: 'VirtualTourContentBuilder',

    components: {
        VirtualTourContentBuilderDialog,
        VirtualTourContentBuilderText,
        VirtualTourContentBuilderImage,
        VirtualTourContentBuilderSlider,
        VirtualTourContentBuilder3d,
        draggable
    },

    props: {

    },

    data() {
        return {
            builderItems: [],
            componentsMapping: {
                'text': 'VirtualTourContentBuilderText',
                'image': 'VirtualTourContentBuilderImage',
                'slider': 'VirtualTourContentBuilderSlider',
                '3d': 'VirtualTourContentBuilder3d',
            }
        }
    },

    methods: {
        openDialog() {
            this.$refs.dialog.open();
        },
        selectItem(itemId) {
            if (this.componentsMapping[itemId]) {
                this.builderItems.push({id: itemId, component: this.componentsMapping[itemId]});
            }
        },
    }
}
</script>

<style lang="scss" scoped>
.builder__add-item {
    border-style: dashed;
}

.builder-item--ghost {
    opacity: 0.5;
    background: #e0e0e0;
}
</style>

