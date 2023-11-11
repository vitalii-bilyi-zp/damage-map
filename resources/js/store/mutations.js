const mutations = {
    setFilters: (state, payload) => {
        state.filters = payload;
    },

    setToken: (state, token) => {
        state.token = token;
    },

    setCurrentUser: (state, user) => {
        state.currentUser = user;
    },
};

export default mutations;
