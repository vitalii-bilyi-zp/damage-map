import queryString from 'query-string';
import Cookies from 'js-cookie';

const ONE_HOUR = 1 / 24;
const ONE_YEAR = 365;

const actions = {
    loadObjectTypes: () => {
        return window.httpClient.get('/api/object-types');
    },

    loadRegions: ({}, payload) => {
        let url = '/api/regions';

        if (payload && payload.loadDetails) {
            url += `?load_details=${payload.loadDetails}`
        }

        return window.httpClient.get(url);
    },

    loadCommunities: () => {
        return window.httpClient.get('/api/communities');
    },

    loadApprovedDamageNotes: () => {
        return window.httpClient.get('/api/damage-notes/approved');
    },

    loadNotApprovedDamageNotes: () => {
        return window.httpClient.get('/api/damage-notes/not-approved');
    },

    saveDamageNote: ({ commit }, payload) => {
        return window.httpClient.post('/api/damage-notes', payload.data);
    },

    saveDamageNotesFromFile: ({ commit }, payload) => {
        return window.httpClient.post('/api/damage-notes/import-file', payload.data);
    },

    exportDamageNotes: () => {
        return window.httpClient.get('/api/damage-notes/export-csv');
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

    loadRegionsData: ({}, payload) => {
        return window.httpClient.get(`/api/damage-notes/regions?${queryString.stringify(payload.params, {encode: false})}`);
    },

    loadDistrictsData: ({}, payload) => {
        return window.httpClient.get(`/api/damage-notes/districts?${queryString.stringify(payload.params, {encode: false})}`);
    },

    loadCommunitiesData: ({}, payload) => {
        return window.httpClient.get(`/api/damage-notes/communities?${queryString.stringify(payload.params, {encode: false})}`);
    },

    setFilters: ({ commit }, filters) => {
        commit('setFilters', filters);
    },

    loadGlobalStatistics: ({ commit }, payload) => {
        return window.httpClient.get(`/api/statistics/global?${queryString.stringify(payload.params, {encode: false})}`);
    },

    loadRatioStatistics: ({ commit }, payload) => {
        return window.httpClient.get(`/api/statistics/ratio?${queryString.stringify(payload.params, {encode: false})}`);
    },

    loadRegulationDocuments: () => {
        return window.httpClient.get('/api/regulation-documents');
    },

    saveRegulationDocument: ({ commit }, payload) => {
        return window.httpClient.post('/api/regulation-documents', payload.data);
    },

    deleteRegulationDocument: ({ commit }, id) => {
        return window.httpClient.delete(`/api/regulation-documents/${id}`);
    },

    loadUsers: () => {
        return window.httpClient.get('/api/users');
    },

    saveUser: ({ commit }, payload) => {
        return window.httpClient.post('/api/users', payload.data);
    },

    loadUser: ({ commit }, id) => {
        return window.httpClient.get(`/api/users/${id}`);
    },

    updateUser: ({ commit }, payload) => {
        const data = {
            ...payload.data,
            '_method': 'PUT',
        }
        return window.httpClient.post(`/api/users/${payload.id}`, data);
    },

    deleteUser: ({ commit }, id) => {
        return window.httpClient.delete(`/api/users/${id}`);
    },

    loadRoles: () => {
        return window.httpClient.get('/api/roles');
    },

    login: ({ commit }, payload) => {
        return new Promise((resolve, reject) => {
            window.httpClient.post('/api/login', payload.data)
                .then((response) => {
                    let user = response.data.user;
                    let token = user.access_token;

                    window.httpClient.bindToken(token);

                    // Check that remember me checkbox is active
                    let expires = payload.remember ? ONE_YEAR : ONE_HOUR;

                    Cookies.set('access_token', token, { expires });

                    commit('setToken', token);
                    commit('setCurrentUser', user);
                    resolve();
                })
                .catch((err) => {
                    reject(err);
                });
        });
    },

    logout: ({ commit }) => {
        return new Promise((resolve, reject) => {
            window.httpClient.get('/api/logout')
                .then(() => {
                    window.httpClient.removeToken();

                    Cookies.remove('access_token');

                    commit('setToken', null);
                    commit('setCurrentUser', null);
                    resolve();
                })
                .catch((err) => {
                    reject(err);
                });
        });
    },

    loadCurrentUser: ({ commit }) => {
        return new Promise((resolve, reject) => {
            window.httpClient.get('/api/user')
                .then((response) => {
                    let user = response.data.user;

                    commit('setCurrentUser', user);
                    resolve();
                })
                .catch((err) => {
                    reject(err);
                });
        });
    },
};

export default actions;
