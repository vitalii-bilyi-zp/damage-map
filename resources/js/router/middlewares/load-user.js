import store from '@/js/store/index';

export default function RedirectIfAuthenticated (router) {
    router.beforeEach(async (to, from, next) => {
        let token = store.state.token;
        let user = store.state.currentUser;

        try {
            // if site have token but dont have user – need to get user
            token && !user && await store.dispatch('loadCurrentUser');
        } catch (err) {
            if (err.response && err.response.status === 401) {
                await store.dispatch('logout');

                return next({
                    name: 'auth.login',
                    params: { nextUrl: to.fullPath }
                });
            }
        }

        return next();
    });
}
