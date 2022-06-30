const actions = {
    loadObjectTypes: () => {
        return window.httpClient.get('/api/object-types');
    },

    loadCommunities: () => {
        return window.httpClient.get('/api/communities');
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
};

export default actions;
