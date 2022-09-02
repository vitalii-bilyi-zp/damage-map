const getters = {
    isAuthorized: state => state.user && state.user.access_token,
};

export default getters;
