const actions = {
    loadObjectTypes: () => {
        return window.httpClient.get('/api/object-types');
    },

    loadCommunities: () => {
        return window.httpClient.get('/api/communities');
    },
};

export default actions;
