import moment from 'moment';

const actions = {
    loadObjectTypes: () => {
        return window.httpClient.get('/api/object-types');
    },

    loadRegions: () => {
        return window.httpClient.get('/api/regions');
    },

    loadCommunities: () => {
        return window.httpClient.get('/api/communities');
    },

    loadDamageNotes: () => {
        return window.httpClient.get('/api/damage-notes');
    },

    saveDamageNotes: ({ commit }, payload) => {
        return window.httpClient.post('/api/damage-notes', payload);
    },

    saveDamageNotesFromFile: ({ commit }, payload) => {
        return window.httpClient.post('/api/damage-notes/file-upload', payload);
    },

    loadRegionsData: () => {
        return window.httpClient.get('/api/damage-notes/regions');
    },

    loadDistrictsData: () => {
        return window.httpClient.get('/api/damage-notes/districts');
    },

    loadCommunitiesData: () => {
        return window.httpClient.get('/api/damage-notes/communities');
    },

    setFilters: ({ commit }, payload) => {
        commit('setFilters', payload);
    },

    loadRatioStatistics: ({ commit }, payload) => {
        // @todo implement
        let mockData = {
            data: {},
        };
        if (payload.params.objectCategory === 1) {
            mockData.data = {
                'Багатоповерховий будинок': 3,
                'Приватний будинок': 2,
                'Гуртожиток': 1,
            };
        } else {
            mockData.data = {
                'Адміністративна будівля': 3,
                'Бізнес-центр': 1,
                'Господарча споруда': 3,
                'Готель / ресторан': 2,
                'Магазин': 2,
                'ТРЦ': 3,
            };
        }

        return new Promise((resolve) => {
            setTimeout(() => {
                resolve(mockData)
            }, 3000);
        });
    },

    loadGlobalStatistics: ({ commit }, payload) => {
        // @todo implement
        let mockData = {
            data: {},
        };
        let [startDate, endDate] = payload.params.period;
        for (let m = moment(startDate); m.isBefore(endDate); m.add(1, 'days')) {
            const date = m.format('YYYY-MM-DD');
            mockData.data[date] = Math.random() * 1000;
        }

        return new Promise((resolve) => {
            setTimeout(() => {
                resolve(mockData)
            }, 3000);
        });
    },
};

export default actions;
