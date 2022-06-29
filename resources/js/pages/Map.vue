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
    </div>
</template>

<script>
    export default {
        name: 'Map',

        data() {
            return {
                isLoading: false,
                dataLoaded: false,
                regionsData: [],
                districtsData: [],
                communitiesData: [],
            }
        },

        mounted() {
            this.loadMapData();
        },

        methods: {
            loadMapData() {
                const promises = [
                    this.$store.dispatch('loadRegionsData'),
                    this.$store.dispatch('loadDistrictsData'),
                    this.$store.dispatch('loadCommunitiesData'),
                ];

                this.isLoading = true;
                Promise.all(promises)
                    .then(([regionsResponse, districtsResponse, communitiesResponse]) => {
                        this.regionsData = regionsResponse.data || [];
                        this.districtsData = districtsResponse.data || [];
                        this.communitiesData = communitiesResponse.data || [];

                        if (!this.dataLoaded) {
                            this.dataLoaded = true;
                            this.initMap();
                        }
                    })
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
                    container: 'map',
                    style: 'mapbox://styles/mapbox/light-v10',
                    center: [33.124, 48.742],
                    //maxBounds: bounds,
                    minZoom: 1,
                    zoom: 5.5
                });

                var zoomStep0 = 5.5;
                var zoomStep1 = 6;
                var zoomStep2 = 7;
                var zoomStep3 = 8;

                var urlregion = 'geojson/region-ukraine-s-v5.geojson';

                var urlrayon = 'geojson/rayon-ukraine-s-v5.geojson';

                var titlerayon = 'geojson/rayon-title.geojson';

                var urlhromada = 'geojson/hromady-ukraine-s-v6.geojson';

                var titlehromada = 'geojson/all-hromady-titles-v3.geojson';

                var hoveredStateId = null;

                map.on('load', function() {
                    var layers = map.getStyle().layers;

                    // Find the index of the first symbol layer in the map style
                    var firstSymbolId;
                    for (var i = 0; i < layers.length; i++) {
                        if (layers[i].type === 'symbol') {
                            firstSymbolId = layers[i].id;
                            break;
                        }
                    }

                    map.addControl(new mapboxgl.NavigationControl(), 'top-left');
                    map.addControl(new mapboxgl.FullscreenControl(), 'top-left');
                    map.addSource('region', {
                        'type': 'geojson',
                        'data': urlregion
                    });
                    map.addSource('titlerayon', {
                        'type': 'geojson',
                        'data': titlerayon
                    });
                    map.addSource('rayon', {
                        'type': 'geojson',
                        'data': urlrayon
                    });
                    map.addSource('titlehromada', {
                        'type': 'geojson',
                        'data': titlehromada
                    });
                    map.addSource('hromada', {
                        'type': 'geojson',
                        'data': urlhromada
                    });

                    map.addLayer({
                        'id': 'region',
                        'type': 'fill',
                        'source': 'region',
                        'maxzoom': zoomStep1,
                        'layout': {},
                        'paint': {
                            'fill-color': [
                                'interpolate',
                                ['linear'],
                                ['get', 'kv'],
                                0,
                                '#F2F12D',
                                245000,
                                '#EED322',
                                490000,
                                '#E6B71E',
                                735000,
                                '#DA9C20',
                                980000,
                                '#CA8323',
                                1225000,
                                '#B86B25',
                                1470000,
                                '#A25626',
                                1710000,
                                '#8B4225',
                                2200000,
                                '#723122'
                            ],
                            'fill-opacity': ['case',
                                ['boolean', ['feature-state', 'hover'], false],
                                0.65,
                                0.5
                            ],

                            'fill-outline-color': '#3A3A33'
                        }
                    },
                    firstSymbolId);

                    map.addLayer({
                        'id': 'region-border',
                        'type': 'line',
                        'source': 'region',
                        'minzoom': zoomStep1,
                        'maxzoom': zoomStep2,
                        'layout': {},
                        'paint': {
                            'line-color': '#3A3A33',
                            'line-width': 1
                        }
                    },
                    firstSymbolId);

                    map.addLayer({
                        'id': 'title-rayon',
                        'type': 'symbol',
                        'source': 'titlerayon',
                        'minzoom': zoomStep1,
                        'maxzoom': zoomStep2,
                        'layout': {
                            'text-field': ['get', 'titlerayon'],
                            'text-font': [
                                'Open Sans Semibold',
                                'Arial Unicode MS Bold'
                            ],
                            'text-size': 10,
                            'text-transform': 'uppercase',
                            'text-offset': [0, 1.25],
                            'text-anchor': 'top'
                        },
                        'paint': {
                            'text-color': '#57574D',
                            'text-opacity': 0.75
                        }
                    });

                    map.addLayer({
                        'id': 'rayon',
                        'type': 'fill',
                        'source': 'rayon',
                        'minzoom': zoomStep1,
                        'maxzoom': zoomStep2,
                        'layout': {},
                        'paint': {
                            'fill-color': [
                                'interpolate',
                                ['linear'],
                                ['get', 'kv'],
                                0,
                                '#FCFCFB',
                                19800,
                                '#F9F9F7',
                                96000,
                                '#F0F0EA',
                                120000,
                                '#E7E7DD',
                                147000,
                                '#D4D4C4',
                                170000,
                                '#C2C2AB',
                                220000,
                                '#AFAF9A',
                                360000,
                                '#747467',
                                1200000,
                                '#57574D'
                            ],
                            'fill-opacity': [
                                'case',
                                ['boolean', ['feature-state', 'hover'], false],
                                0.65,
                                0.5
                            ],
                            'fill-outline-color': '#3A3A33'
                        }
                    },
                    firstSymbolId);

                    map.addLayer({
                        'id': 'rayon-border',
                        'type': 'line',
                        'source': 'rayon',
                        'minzoom': zoomStep2,
                        'layout': {},
                        'paint': {
                            'line-color': '#3A3A33',
                            'line-width': 1
                        }
                    },
                    firstSymbolId);

                    map.addLayer({
                        'id': 'title-hromada',
                        'type': 'symbol',
                        'source': 'titlehromada',
                        'minzoom': zoomStep2,
                        'layout': {
                            'text-field': ['get', 'hromada'],
                            'text-font': [
                                'Open Sans Semibold',
                                'Arial Unicode MS Bold'
                            ],
                            'text-size': 9,
                            'text-transform': 'uppercase',
                            'text-offset': [0, 1.25],
                            'text-anchor': 'top'
                        },
                        'paint': {
                            'text-color': '#57574D',
                            'text-opacity': 0.9
                        }
                    });

                    map.addLayer({
                        'id': 'hromada',
                        'type': 'fill',
                        'source': 'hromada',
                        'minzoom': zoomStep2,
                        'layout': {},
                        'layout': {},
                        'paint': {
                            'fill-color': [
                                'interpolate',
                                ['linear'],
                                ['get', 'voters'],
                                0,
                                '#fdfdb0',
                                5000,
                                '#EED322',
                                10000,
                                '#E6B71E',
                                20000,
                                '#DA9C20',
                                50000,
                                '#CA8323',
                                100000,
                                '#B86B25',
                                500000,
                                '#A25626',
                                1000000,
                                '#8B4225',
                                2500000,
                                '#723122'
                            ],
                            'fill-opacity': [
                                'case',
                                ['boolean', ['feature-state', 'hover'], false],
                                0.65,
                                0.5
                            ],

                            'fill-outline-color': '#723122'
                        }
                    },
                    firstSymbolId);

                    map.on('click', 'region', function(e) {
                        const properties = e.features[0].properties;
                        const html = `
                            <h4 class="region">${properties.region}</h4>
                            <div>
                                <table>
                                    <tbody>
                                        <tr>
                                            <td>Вартість відновлення</td>
                                            <td><span>${ 0 }</span> грн.</td>
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

                    map.on('click', 'rayon', function(e) {
                        const properties = e.features[0].properties;
                        const html = `
                            <h4 class="rayon">${properties.rayon}</h4>
                            <div>
                                <table>
                                    <tbody>
                                        <tr>
                                            <td>Вартість відновлення</td>
                                            <td><span>${ 0 }</span> грн.</td>
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

                    map.on('click', 'hromada', function(e) {
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
                                            <td><span>${ 0 }</span> грн.</td>
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
        },
    }
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
        background-color: #C2C2AB;
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

    .mapboxgl-popup-content thead tr th:first-child, tbody tr td:first-child {
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
        max-width: 400px;
        font-size: 12px;
    }

    .mapboxgl-popup-content {
        width: 300px;
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
</style>

