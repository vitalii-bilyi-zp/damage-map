<template>
    <div class="slider">
        <section
            :id="`main-slider-${id}`"
            class="splide main-slider mb-4"
            aria-label="Beautiful Images"
        >
            <div class="splide__track">
                <ul class="splide__list">
                    <li class="splide__slide" v-for="image in images" :key="image">
                        <img :src="image" alt="">
                    </li>
                </ul>
            </div>
        </section>

        <section
            :id="`thumbnail-slider-${id}`"
            class="splide thumbnail-slider"
            aria-label="The carousel with thumbnails. Selecting a thumbnail will change the Beautiful Gallery carousel."
        >
            <div class="splide__track">
                <ul class="splide__list">
                    <li class="splide__slide" v-for="image in images" :key="image">
                        <img :src="image" alt="">
                    </li>
                </ul>
            </div>
        </section>
    </div>
</template>

<script>
    import Splide from '@splidejs/splide';

    export default {
        name: "Slider",

        props: {
            id: {
                type: String | Number
            },
            images: {
                type: Array,
                default: () => []
            },
        },

        data() {
            return {

            }
        },

        mounted() {
            this.initCarousel();
        },

        methods: {
            initCarousel() {
                var main = new Splide(`#main-slider-${this.id}`, {
                    type: 'fade',
                    heightRatio: 0.5,
                    pagination: false,
                    arrows: false,
                    cover: true,
                });

                var thumbnails = new Splide(`#thumbnail-slider-${this.id}`, {
                    rewind: true,
                    fixedWidth: 156,
                    fixedHeight: 98,
                    isNavigation: true,
                    // drag: false,
                    gap: 10,
                    focus: 'center',
                    pagination: false,
                    cover: true,
                    dragMinThreshold: {
                        mouse: 4,
                        touch: 10,
                    },
                    breakpoints : {
                        640: {
                        fixedWidth: 66,
                        fixedHeight: 38,
                        },
                    },
                });

                main.sync( thumbnails );
                main.mount();
                thumbnails.mount();
            }
        }
    }
</script>

<style lang="scss" scoped>
.slider {
    padding: 16px 50px;
    border-radius: 4px;
    background-color: rgba(0, 0, 0, 0.05);
}

.thumbnail-slider {
    .splide__list {
        padding: 0 20px;
    }

    .splide__arrow--prev {
        left: -40px;
    }

    .splide__arrow--next {
        right: -40px;
    }
}

.splide__slide {
  opacity: 0.6;
}

.splide__slide.is-active {
  opacity: 1;
}
</style>

<style lang="scss">
.thumbnail-slider {
    .splide__arrow--prev {
        left: -40px;
    }

    .splide__arrow--next {
        right: -40px;
    }
}
</style>
