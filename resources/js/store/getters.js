const getters = {
    isAuthorized: state => state.currentUser && state.currentUser.access_token,
    isSuperAdmin: state => state.currentUser && state.currentUser.roles && state.currentUser.roles.includes('super_admin'),
    isAdmin: state => state.currentUser && state.currentUser.roles && state.currentUser.roles.includes('admin'),
    isAnalyst: state => state.currentUser && state.currentUser.roles && state.currentUser.roles.includes('analyst'),
};

export default getters;
