<template>
    <div class="map">
        <div v-if="isLoading" class="map__loader">
            <v-progress-circular
                color="#757575"
                :size="60"
                :width="3"
                indeterminate
            />
        </div>

        <div id="map"></div>

        <v-card class="map-card">
            <v-toolbar flat color="white" height="64">
                <v-row>
                    <v-col cols="12">
                        <v-autocomplete
                            v-model="region"
                            :items="regionItems"
                            clearable
                            dense
                            outlined
                            hide-details
                            item-text="name"
                            item-value="id"
                            placeholder="Регіон"
                            :disabled="!regionItems || !regionItems.length"
                        ></v-autocomplete>
                    </v-col>
                </v-row>
            </v-toolbar>
        </v-card>
    </div>
</template>

<script>
import regions from "@/js/data/regions.json";
import districts from "@/js/data/districts.json";
import districtTitles from "@/js/data/district-titles.json";
import communities from "@/js/data/communities.json";
import communityTitles from "@/js/data/community-titles.json";

export default {
    name: "Map",

    data() {
        return {
            isLoading: false,
            dataLoaded: false,
            regionsData: [],
            districtsData: [],
            communitiesData: [],
            region: null,
        };
    },

    computed: {
        regionItems() {
            return regions.features.map((item) => {
                return {
                    id: item.properties.fid,
                    name: item.properties.region,
                };
            });
        },
        regionsMapping() {
            const communitiesMapping = communities.features.reduce(
                (prev, curr) => {
                    if (curr.properties.region_fid) {
                        if (!prev[curr.properties.region_fid]) {
                            prev[curr.properties.region_fid] = {};
                        }
                        prev[curr.properties.region_fid][curr.properties.fid] = true;
                    }

                    return prev;
                },
                {}
            );
            const districtsMapping = districts.features.reduce((prev, curr) => {
                if (curr.properties.region_fid) {
                    if (!prev[curr.properties.region_fid]) {
                        prev[curr.properties.region_fid] = {};
                    }
                    prev[curr.properties.region_fid][curr.properties.fid] = true;
                }

                return prev;
            }, {});

            return regions.features.reduce((prev, curr) => {
                if (!prev[curr.properties.fid]) {
                    prev[curr.properties.fid] = {};
                }
                prev[curr.properties.fid]["districts"] = districtsMapping[curr.properties.fid] || {};
                prev[curr.properties.fid]["communities"] = communitiesMapping[curr.properties.fid] || {};

                return prev;
            }, {});
        },
    },

    watch: {
        region() {
            this.initMap();
        },
    },

    mounted() {
        this.loadMapData();
    },

    methods: {
        loadMapData() {
            const promises = [
                this.$store.dispatch("loadRegionsData"),
                this.$store.dispatch("loadDistrictsData"),
                this.$store.dispatch("loadCommunitiesData"),
            ];

            this.isLoading = true;
            Promise.all(promises)
                .then(
                    ([
                        regionsResponse,
                        districtsResponse,
                        communitiesResponse,
                    ]) => {
                        this.regionsData = regionsResponse.data || [];
                        this.districtsData = districtsResponse.data || [];
                        this.communitiesData = communitiesResponse.data || [];

                        if (!this.dataLoaded) {
                            this.dataLoaded = true;
                            this.initMap();
                        }
                    }
                )
                .catch(() => {
                    //
                })
                .finally(() => {
                    this.isLoading = false;
                });
        },

        initMap() {
            mapboxgl.accessToken = process.env.MIX_MAPBOXGL_TOKEN;

            // var bounds = [
            //     [20.435, 42.779],
            //     [42.935, 53.278]
            // ];

            var map = new mapboxgl.Map({
                container: "map",
                style: "mapbox://styles/mapbox/light-v10",
                center: [33.124, 48.742],
                //maxBounds: bounds,
                minZoom: 1,
                zoom: 5.5,
            });

            // var zoomStep0 = 5.5;
            var zoomStep1 = 6;
            var zoomStep2 = 7;
            // var zoomStep3 = 8;

            const self = this;

            map.on("load", function () {
                var layers = map.getStyle().layers;

                // Find the index of the first symbol layer in the map style
                var firstSymbolId;
                for (var i = 0; i < layers.length; i++) {
                    if (layers[i].type === "symbol") {
                        firstSymbolId = layers[i].id;
                        break;
                    }
                }

                const regionsComputed = self.getRegionsGeojson();
                const districtsTitlesComputed = self.getDistrictsTitlesGeojson();
                const districtsComputed = self.getDistrictsGeojson();
                const communitiesTitlesComputed = self.getCommunitiesTitlesGeojson();
                const communitiesComputed = self.getCommunitiesGeojson();

                map.addControl(new mapboxgl.NavigationControl(), "top-left");
                map.addControl(new mapboxgl.FullscreenControl(), "top-left");
                map.addSource("region", {
                    type: "geojson",
                    data: regionsComputed,
                });
                map.addSource("titlerayon", {
                    type: "geojson",
                    data: districtsTitlesComputed,
                });
                map.addSource("rayon", {
                    type: "geojson",
                    data: districtsComputed,
                });
                map.addSource("titlehromada", {
                    type: "geojson",
                    data: communitiesTitlesComputed,
                });
                map.addSource("hromada", {
                    type: "geojson",
                    data: communitiesComputed,
                });

                map.addLayer(
                    {
                        id: "region",
                        type: "fill",
                        source: "region",
                        maxzoom: zoomStep1,
                        layout: {},
                        paint: {
                            "fill-color": [
                                "interpolate",
                                ["linear"],
                                ["get", "restorationСost"],
                                0,
                                "#fffe5d",
                                125000000,
                                "#fae554",
                                250000000,
                                "#f6d34d",
                                375000000,
                                "#eda83d",
                                500000000,
                                "#e57d2d",
                                625000000,
                                "#d83d16",
                                750000000,
                                "#de5c21",
                                875000000,
                                "#d3210c",
                                1000000000,
                                "#cc0000",
                            ],
                            "fill-opacity": [
                                "case",
                                ["boolean", ["feature-state", "hover"], false],
                                0.65,
                                0.5,
                            ],
                            "fill-outline-color": "#3A3A33",
                        },
                    },
                    firstSymbolId
                );

                map.addLayer(
                    {
                        id: "region-border",
                        type: "line",
                        source: "region",
                        minzoom: zoomStep1,
                        maxzoom: zoomStep2,
                        layout: {},
                        paint: {
                            "line-color": "#3A3A33",
                            "line-width": 1,
                        },
                    },
                    firstSymbolId
                );

                map.addLayer({
                    id: "title-rayon",
                    type: "symbol",
                    source: "titlerayon",
                    minzoom: zoomStep1,
                    maxzoom: zoomStep2,
                    layout: {
                        "text-field": ["get", "titlerayon"],
                        "text-font": [
                            "Open Sans Semibold",
                            "Arial Unicode MS Bold",
                        ],
                        "text-size": 10,
                        "text-transform": "uppercase",
                        "text-offset": [0, 1.25],
                        "text-anchor": "top",
                    },
                    paint: {
                        "text-color": "#57574D",
                        "text-opacity": 0.75,
                    },
                });

                map.addLayer(
                    {
                        id: "rayon",
                        type: "fill",
                        source: "rayon",
                        minzoom: zoomStep1,
                        maxzoom: zoomStep2,
                        layout: {},
                        paint: {
                            "fill-color": [
                                "interpolate",
                                ["linear"],
                                ["get", "restorationСost"],
                                0,
                                "#FCFCFB",
                                31250000,
                                "#F9F9F7",
                                62500000,
                                "#F0F0EA",
                                93750000,
                                "#E7E7DD",
                                125000000,
                                "#D4D4C4",
                                156250000,
                                "#C2C2AB",
                                187500000,
                                "#AFAF9A",
                                218750000,
                                "#747467",
                                250000000,
                                "#57574D",
                            ],
                            "fill-opacity": [
                                "case",
                                ["boolean", ["feature-state", "hover"], false],
                                0.65,
                                0.5,
                            ],
                            "fill-outline-color": "#3A3A33",
                        },
                    },
                    firstSymbolId
                );

                map.addLayer(
                    {
                        id: "rayon-border",
                        type: "line",
                        source: "rayon",
                        minzoom: zoomStep2,
                        layout: {},
                        paint: {
                            "line-color": "#3A3A33",
                            "line-width": 1,
                        },
                    },
                    firstSymbolId
                );

                map.addLayer({
                    id: "title-hromada",
                    type: "symbol",
                    source: "titlehromada",
                    minzoom: zoomStep2,
                    layout: {
                        "text-field": ["get", "hromada"],
                        "text-font": [
                            "Open Sans Semibold",
                            "Arial Unicode MS Bold",
                        ],
                        "text-size": 9,
                        "text-transform": "uppercase",
                        "text-offset": [0, 1.25],
                        "text-anchor": "top",
                    },
                    paint: {
                        "text-color": "#57574D",
                        "text-opacity": 0.9,
                    },
                });

                map.addLayer(
                    {
                        id: "hromada",
                        type: "fill",
                        source: "hromada",
                        minzoom: zoomStep2,
                        layout: {},
                        layout: {},
                        paint: {
                            "fill-color": [
                                "interpolate",
                                ["linear"],
                                ["get", "restorationСost"],
                                0,
                                "#fdfdb0",
                                50000,
                                "#EED322",
                                100000,
                                "#E6B71E",
                                200000,
                                "#DA9C20",
                                500000,
                                "#CA8323",
                                1000000,
                                "#B86B25",
                                5000000,
                                "#A25626",
                                10000000,
                                "#8B4225",
                                25000000,
                                "#723122",
                            ],
                            "fill-opacity": [
                                "case",
                                ["boolean", ["feature-state", "hover"], false],
                                0.65,
                                0.5,
                            ],

                            "fill-outline-color": "#723122",
                        },
                    },
                    firstSymbolId
                );

                map.on("click", "region", function (e) {
                    const properties = e.features[0].properties;
                    const html = `
                            <h4 class="region">${properties.region}</h4>
                            <div>
                                <table>
                                    <tbody>
                                        <tr>
                                            <td>Вартість відновлення</td>
                                            <td><span>${properties.restorationСost}</span> грн.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        `;

                    new mapboxgl.Popup()
                        .setLngLat(e.lngLat)
                        .setHTML(html)
                        .addTo(map);
                });

                map.on("click", "rayon", function (e) {
                    const properties = e.features[0].properties;
                    const html = `
                            <h4 class="rayon">${properties.rayon}</h4>
                            <div>
                                <table>
                                    <tbody>
                                        <tr>
                                            <td>Вартість відновлення</td>
                                            <td><span>${properties.restorationСost}</span> грн.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        `;

                    new mapboxgl.Popup()
                        .setLngLat(e.lngLat)
                        .setHTML(html)
                        .addTo(map);
                });

                map.on("click", "hromada", function (e) {
                    const properties = e.features[0].properties;
                    const html = `
                            <h4 class="region">${properties.hromada}</h4>
                            <div>
                                <p>
                                    <span>${properties.region}</span><br>
                                    ${properties.rayon}
                                </p>
                            </div>
                            <div>
                                <table>
                                    <tbody>
                                        <tr>
                                            <td>Вартість відновлення</td>
                                            <td><span>${properties.restorationСost}</span> грн.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        `;

                    new mapboxgl.Popup()
                        .setLngLat(e.lngLat)
                        .setHTML(html)
                        .addTo(map);
                });
            });
        },

        getRegionsGeojson() {
            const regionsMapping = this.regionsData.reduce((prev, curr) => {
                prev[curr.name] = curr;
                return prev;
            }, {});

            let oldFeatures = regions.features;
            if (this.region) {
                const feature = oldFeatures.find(
                    (item) => item.properties.fid === this.region
                );
                oldFeatures = [feature];
            }

            const newFeatures = oldFeatures.map((item) => {
                let restorationСost = 0;

                if (regionsMapping[item.properties.region]) {
                    restorationСost = +regionsMapping[item.properties.region].restoration_cost || 0;
                }

                return {
                    ...item,
                    properties: {
                        fid: item.properties.fid,
                        region: item.properties.region,
                        restorationСost,
                    },
                };
            });

            return {
                ...regions,
                features: newFeatures,
            };
        },

        getDistrictsTitlesGeojson() {
            if (!this.region) {
                return districtTitles;
            }

            const districts = this.regionsMapping[this.region]["districts"] || {};
            const newFeatures = districtTitles.features.filter(
                (item) => !!districts[item.properties.fid]
            );

            return {
                ...districtTitles,
                features: newFeatures,
            };
        },

        getDistrictsGeojson() {
            const districtsMapping = this.districtsData.reduce((prev, curr) => {
                prev[curr.name] = curr;
                return prev;
            }, {});

            let oldFeatures = districts.features;
            if (this.region) {
                const districts = this.regionsMapping[this.region]["districts"] || {};
                oldFeatures = oldFeatures.filter(
                    (item) => !!districts[item.properties.fid]
                );
            }

            const newFeatures = oldFeatures.map((item) => {
                let restorationСost = 0;

                if (districtsMapping[item.properties.rayon]) {
                    restorationСost = +districtsMapping[item.properties.rayon].restoration_cost || 0;
                }

                return {
                    ...item,
                    properties: {
                        fid: item.properties.fid,
                        rayon: item.properties.rayon,
                        restorationСost,
                    },
                };
            });

            return {
                ...districts,
                features: newFeatures,
            };
        },

        getCommunitiesTitlesGeojson() {
            if (!this.region) {
                return communityTitles;
            }

            const communities = this.regionsMapping[this.region]["communities"] || {};
            const newFeatures = communityTitles.features.filter(
                (item) => !!communities[item.properties.fid]
            );

            return {
                ...communityTitles,
                features: newFeatures,
            };
        },

        getCommunitiesGeojson() {
            const communitiesMapping = this.communitiesData.reduce(
                (prev, curr) => {
                    prev[curr.name] = curr;
                    return prev;
                },
                {}
            );

            let oldFeatures = communities.features;
            if (this.region) {
                const communities = this.regionsMapping[this.region]["communities"] || {};
                oldFeatures = oldFeatures.filter(
                    (item) => !!communities[item.properties.fid]
                );
            }

            const newFeatures = oldFeatures.map((item) => {
                let restorationСost = 0;

                if (communitiesMapping[item.properties.hromada]) {
                    restorationСost = +communitiesMapping[item.properties.hromada].restoration_cost || 0;
                }

                return {
                    ...item,
                    properties: {
                        region: item.properties.region,
                        rayon: item.properties.rayon,
                        hromada: item.properties.hromada,
                        restorationСost,
                    },
                };
            });

            return {
                ...communities,
                features: newFeatures,
            };
        },
    },
};
</script>

<style lang="scss" scoped>
.map {
    position: relative;
    height: 100%;
}

.map__loader {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1;
}

#map {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
}

::v-deep {
    .mapboxgl-popup-content div {
        padding: 10px;
    }

    .mapboxgl-popup-content .region {
        background-color: #f4e470;
    }

    .mapboxgl-popup-content .rayon {
        background-color: #c2c2ab;
    }

    .mapboxgl-popup-content h4 {
        text-transform: uppercase;
        padding: 10px;
        margin-top: 0px;
    }

    .mapboxgl-popup-content ul {
        padding-left: 10px;
        list-style: none;
    }

    .mapboxgl-popup-content table {
        width: 100%;
        box-sizing: border-box;
    }

    .mapboxgl-popup-content table td {
        border: 1px solid #dbdbdb;
        border-width: 0 0 1px;
    }

    .mapboxgl-popup-content thead tr th:first-child,
    tbody tr td:first-child {
        width: 150px;
        min-width: 150px;
        max-width: 150px;
    }

    .mapboxgl-popup-content p {
        margin-bottom: 0;
    }

    .mapboxgl-popup-content span {
        text-transform: uppercase;
    }

    .mapboxgl-popup {
        font-size: 12px;

        @media screen and (min-width: 768px) {
            max-width: 320px !important;
        }
    }

    .mapboxgl-popup-content {
        padding: 15px;
    }

    .mapboxgl-popup-close-button {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 16px;
        height: 16px;
        font-size: 16px;
    }
}

.map-card {
    position: absolute;
    top: 10px;
    right: 10px;
    width: 300px;
    max-width: calc(100% - 60px);
}
</style>
