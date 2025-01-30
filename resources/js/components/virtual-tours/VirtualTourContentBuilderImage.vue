<template>
    <v-expansion-panels v-model="panel" class="builder-item">
        <v-expansion-panel>
            <v-expansion-panel-header>
                <div class="d-flex align-center">
                    <img
                        class="builder-item__header-image mr-3"
                        src="/storage/images/image.png"
                    />
                    <b>Зображення</b>
                </div>
            </v-expansion-panel-header>
                <v-expansion-panel-content>
                    Some content
                </v-expansion-panel-content>
        </v-expansion-panel>
    </v-expansion-panels>
</template>

<script>
export default {
    name: 'VirtualTourContentBuilderImage',

    props: {

    },

    data() {
        return {
            panel: [],
        }
    },
}
</script>

<style lang="scss">

</style>
