const getters = {
    isAuthorized: state => state.currentUser && state.currentUser.access_token,
};

export default getters;
