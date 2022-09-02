const mutations = {
    setFilters: (state, payload) => {
        state.filters = payload;
    },

    setToken: (state, token) => {
        state.token = token;
    },

    setUser: (state, user) => {
        state.user = user;
    },
};

export default mutations;
