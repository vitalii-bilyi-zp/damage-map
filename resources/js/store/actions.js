import queryString from 'query-string';
import Cookies from 'js-cookie';

const ONE_HOUR = 1 / 24;
const ONE_YEAR = 365;

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

    loadGlobalStatistics: ({ commit }, payload) => {
        return window.httpClient.get(`/api/statistics/global?${queryString.stringify(payload.params, {encode: false})}`);
    },

    loadRatioStatistics: ({ commit }, payload) => {
        return window.httpClient.get(`/api/statistics/ratio?${queryString.stringify(payload.params, {encode: false})}`);
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
                    commit('setUser', user);
                    resolve();
                })
                .catch((err) => {
                    reject(err);
                });
        });
    },

    loadUser: ({ commit }) => {
        return new Promise((resolve, reject) => {
            window.httpClient.get('/api/user')
                .then((response) => {
                    let user = response.data.user;

                    commit('setUser', user);
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
                    commit('setUser', null);
                    resolve();
                })
                .catch((err) => {
                    reject(err);
                });
        });
    },
};

export default actions;
