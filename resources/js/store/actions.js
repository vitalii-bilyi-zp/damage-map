import moment from 'moment';

import queryString from 'query-string';

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
        return window.httpClient.post('/api/damage-notes', payload.data);
    },

    saveDamageNotesFromFile: ({ commit }, payload) => {
        return window.httpClient.post('/api/damage-notes/file-upload', payload.data);
    },

    loadDamageNote: ({ commit }, id) => {
        return window.httpClient.get(`/api/damage-notes/${id}`);
    },

    updateDamageNote: ({ commit }, payload) => {
        const data = {
            ...payload.data,
            '_method': 'PUT',
        }
        return window.httpClient.post(`/api/damage-notes/${payload.id}`, data);
    },

    deleteDamageNote: ({ commit }, id) => {
        return window.httpClient.delete(`/api/damage-notes/${id}`);
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

    setFilters: ({ commit }, filters) => {
        commit('setFilters', filters);
    },

    loadRatioStatistics: ({ commit }, payload) => {
        return window.httpClient.get(`/api/statistics/ratio?${queryString.stringify(payload.params, {encode: false})}`);
    },

    loadGlobalStatistics: ({ commit }, payload) => {
        // @todo implement
        let mockData = {
            data: {},
        };

        for (let m = moment(payload.params.start_date); m.isBefore(payload.params.end_date); m.add(1, 'days')) {
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
