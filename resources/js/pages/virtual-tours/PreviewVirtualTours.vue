<template>
    <v-container fluid>
        <v-row class="ma-0" justify="center">
            <v-col cols="12">
                <v-card>
                    <div class="card-header pa-4">
                        <h1 class="card-header__title">Віртуальні тури</h1>
                        <p class="card-header__subtitle">Ознайомтесь із зруйнованими об'єктами інфраструктури та культурної спадщини у форматі реалістичних турів. Збережіть пам’ять про важливі місця та підтримайте ініціативи їх відновлення, переглядаючи ці унікальні цифрові реконструкції.</p>
                    </div>

                    <v-divider></v-divider>

                    <v-card-text class="pa-4">
                        <v-row class="ma-0">
                            <v-col cols="3">
                                <v-card class="tour-card" @click="$router.push({ name: 'virtual-tours.tour.preview', params: { id: 1 } })">
                                    <div class="tour-card__image-wrapper">
                                        <v-img
                                            src="/storage/images/slider/mlyn/mlyn_3_1.jpg"
                                            height="200px"
                                        ></v-img>

                                        <!-- <img src="/storage/images/3d.svg" class="tour-card__3d-image" /> -->
                                    </div>

                                    <v-card-title style="word-break: break-word;">
                                        Млин Германа Нібура
                                    </v-card-title>

                                    <v-card-subtitle>
                                        м. Запоріжжя, вул. Сергія Серікова, 30
                                    </v-card-subtitle>
                                </v-card>
                            </v-col>

                            <v-col cols="3">
                                <v-card class="tour-card" @click="$router.push({ name: 'virtual-tours.tour.preview', params: { id: 2 } })">
                                    <div class="tour-card__image-wrapper">
                                        <v-img
                                            src="/storage/images/slider/vokzal/vokzal_2_1.jpg"
                                            height="200px"
                                        ></v-img>

                                        <!-- <img src="/storage/images/3d.svg" class="tour-card__3d-image" /> -->
                                    </div>

                                    <v-card-title style="word-break: break-word;">
                                        Вокзал «Олександрівськ»
                                    </v-card-title>

                                    <v-card-subtitle>
                                        м. Запоріжжя, вул. Костянтина Великого, 9
                                    </v-card-subtitle>
                                </v-card>
                            </v-col>

                            <v-col cols="3">
                                <v-card class="tour-card" @click="$router.push({ name: 'virtual-tours.tour.preview', params: { id: 3 } })">
                                    <div class="tour-card__image-wrapper">
                                        <v-img
                                            src="/storage/images/slider/zhinoch/zhinoch_2_1.jpg"
                                            height="200px"
                                        ></v-img>

                                        <!-- <img src="/storage/images/3d.svg" class="tour-card__3d-image" /> -->
                                    </div>

                                    <v-card-title style="word-break: break-word;">
                                        Будинок жіночої гімназії
                                    </v-card-title>

                                    <v-card-subtitle>
                                        м. Оріхів вул. Шевченка, 16
                                    </v-card-subtitle>
                                </v-card>
                            </v-col>

                            <v-col cols="3">
                                <v-card class="tour-card" @click="$router.push({ name: 'virtual-tours.tour.preview', params: { id: 4 } })">
                                    <div class="tour-card__image-wrapper">
                                        <v-img
                                            src="/storage/images/slider/cholovich/cholovich_2_1.jpg"
                                            height="200px"
                                        ></v-img>

                                        <!-- <img src="/storage/images/3d.svg" class="tour-card__3d-image" /> -->
                                    </div>

                                    <v-card-title style="word-break: break-word;">
                                        Будинок чоловічого реального училища
                                    </v-card-title>

                                    <v-card-subtitle>
                                        м. Оріхів вул. Шевченка, 16
                                    </v-card-subtitle>
                                </v-card>
                            </v-col>

                            <v-col cols="3">
                                <v-card class="tour-card" @click="$router.push({ name: 'virtual-tours.tour.preview', params: { id: 5 } })">
                                    <div class="tour-card__image-wrapper">
                                        <v-img
                                            src="/storage/images/slider/torgov/torgov_2_1.jpg"
                                            height="200px"
                                        ></v-img>

                                        <!-- <img src="/storage/images/3d.svg" class="tour-card__3d-image" /> -->
                                    </div>

                                    <v-card-title style="word-break: break-word;">
                                        Будинок торгових рядів
                                    </v-card-title>

                                    <v-card-subtitle>
                                        м. Оріхів вул. Покровська, 39
                                    </v-card-subtitle>
                                </v-card>
                            </v-col>

                            <v-col cols="3">
                                <v-card class="tour-card" @click="$router.push({ name: 'virtual-tours.tour.preview', params: { id: 6 } })">
                                    <div class="tour-card__image-wrapper">
                                        <v-img
                                            src="/storage/images/slider/muzei/muzei_2_1.jpg"
                                            height="200px"
                                        ></v-img>

                                        <!-- <img src="/storage/images/3d.svg" class="tour-card__3d-image" /> -->
                                    </div>

                                    <v-card-title style="word-break: break-word;">
                                        Гуляйпільський краєзнавчий музей
                                    </v-card-title>

                                    <v-card-subtitle>
                                        м. Гуляйполе вул. Соборна, 75
                                    </v-card-subtitle>
                                </v-card>
                            </v-col>

                            <v-col cols="3">
                                <v-card class="tour-card" @click="$router.push({ name: 'virtual-tours.tour.preview', params: { id: 7 } })">
                                    <div class="tour-card__image-wrapper">
                                        <v-img
                                            src="/storage/images/slider/mlyn_nadiya/mlyn_2_1.jpg"
                                            height="200px"
                                        ></v-img>

                                        <!-- <img src="/storage/images/3d.svg" class="tour-card__3d-image" /> -->
                                    </div>

                                    <v-card-title style="word-break: break-word;">
                                        Паровий млин «Надія»
                                    </v-card-title>

                                    <v-card-subtitle>
                                        м. Гуляйполе вул. Соборна, 115
                                    </v-card-subtitle>
                                </v-card>
                            </v-col>
                        </v-row>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
    export default {
        name: 'VirtualTours',

        data() {
            return {

            }
        },
    }
</script>

<style lang="scss" scoped>
.card-header {
    text-align: center;
    max-width: 800px;
    margin: 0 auto 10px;
}

.card-header__title {
    font-size: 28px;
    font-weight: 500;
    color: rgba(46, 38, 61, 0.9);
}

.card-header__subtitle {
    margin: 10px 0 0;
    font-size: 16px;
    color: rgba(46, 38, 61, 0.7);
}

.tour-card__image-wrapper {
    position: relative;
}

.tour-card__3d-image {
    position: absolute;
    width: 70px;
    height: auto;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}
</style>
